#!/usr/bin/php
<?php
/**
 * Moodle module forum
 * Munin plugin to count new posts on forums
 *
 * It's required to define a container entry for this plugin in your
 * /etc/munin/plugin-conf.d/munin-node (or a separate and dedicated file).
 *
 * @example Example entry for configuration:
 * [moodle*]
 * env.type mysql
 * env.db moodle
 * env.user mysql_user
 * env.pass mysql_pass
 * env.host localhost
 * env.port 3306
 * env.table_prefix mdl_
 *
 * @author Arnaud Trouvé <ak4t0sh@free.fr>
 * @version 1.0 2014
 *
 */
$dbh = null;
$db = getenv('db');
$type = getenv('type');
$host = getenv('host');
$user = getenv('user');
$pass = getenv('pass');
$table_prefix = getenv('table_prefix');
$port = getenv('port');
if (!$port)
    $port = 3306;
//$graph_period = getenv('graph_period');
$graph_period = time() - 5*60;


if (count($argv) === 2 && $argv[1] === 'config') {
    echo "graph_title Moodle Forum Posts\n";
    echo "graph_args --base 1000 --lower-limit 0\n";
    echo "graph_vlabel number\n";
    echo "graph_category Moodle\n";
    echo "graph_scale no\n";
    echo "graph_info Displays the sum of new forums posts / discussions in your Moodle site\n";
    echo "forum_posts.label posts\n";
    echo "forum_posts.min 0\n";
    echo "forum_posts.draw AREA\n";
    exit(0);
}

try {
    $dsn = $type . ':host=' . $host . ';port=' . $port . ';dbname=' . $db;
    $dbh = new PDO($dsn, $user, $pass);
} catch (Exception $e) {
    echo "Connection failed\n";
    exit(1);
}

$nb = 0;
if (($stmt = $dbh->query("SELECT count(id) FROM {$table_prefix}forum_posts WHERE modified > $graph_period")) != false) {
    $nb = $stmt->fetchColumn();
}
echo "forum_posts.value $nb\n";
