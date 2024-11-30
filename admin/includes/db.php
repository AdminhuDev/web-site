<?php
                define('DB_HOST', 'localhost');
                define('DB_NAME', 'portfolio_adminhu');
                define('DB_USER', 'portfolio_user');
                define('DB_PASS', 'Portfolio@2452');
                
                try {
                    $pdo = new PDO(
                        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
                        DB_USER,
                        DB_PASS,
                        [
                            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4'
                        ]
                    );
                } catch (PDOException $e) {
                    error_log('Erro de conexão: ' . $e->getMessage());
                    die('Erro ao conectar ao banco de dados');
                }
            