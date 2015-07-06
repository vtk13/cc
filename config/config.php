<?php
use Vtk13\LibSql\IDatabase;
use Vtk13\LibSql\Mysql\Mysql;

return [
    IDatabase::class => function() {
        $db = new Mysql('localhost', 'root', '', 'cc');
        $db->query('SET NAMES utf8');
        return $db;
    },
    Twig_Environment::class => function() {
        $loader = new Twig_Loader_Filesystem(realpath(__DIR__ . '/../templates'));
        return new Twig_Environment($loader, array(
            'cache' => realpath(__DIR__ . '/../var/template_cache'),
            'auto_reload' => true,
        ));
    },
];
