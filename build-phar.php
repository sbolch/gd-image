<?php

@unlink('converter.phar');
$phar = new Phar('converter.phar');
$phar->startBuffering();
$phar->buildFromDirectory(__DIR__ . '/src');
$phar->setStub("#!/usr/bin/env php \n".$phar->createDefaultStub('cli/converter.php'));
$phar->stopBuffering();
$phar->compressFiles(Phar::GZ);
chmod('converter.phar', 0775);
