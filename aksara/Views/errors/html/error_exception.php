<?php
use Config\Services;
use CodeIgniter\CodeIgniter;

$errorId = uniqid('error', true);
?>
<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="robots" content="noindex">
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="viewport" content="user-scalable=no, width=device-width, height=device-height, initial-scale=1, maximum-scale=1" />

    <title><?= esc($title) ?></title>
    <style>
        <?= preg_replace('#[\r\n\t ]+#', ' ', file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'debug.css')) ?>
    </style>

    <script>
        <?= file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'debug.js') ?>
    </script>
</head>
<body onload="init()">

    <!-- Header -->
    <div class="header">
        <div class="container">
            <h1><?= esc($title), esc($exception->getCode() ? ' #' . $exception->getCode() : '') ?></h1>
            <p>
                <?= nl2br(esc($exception->getMessage())) ?>
                <a href="//www.google.com/search?q=<?= urlencode($title . ' ' . preg_replace('#\'.*\'|".*"#Us', '', $exception->getMessage())) ?>"
                   rel="noreferrer" target="_blank">search &rarr;</a>
            </p>
        </div>
    </div>

    <!-- Source -->
    <div class="container">
        <p><b><?= esc(clean_path($file)) ?></b> at line <b><?= esc($line) ?></b></p>

        <?php if (is_file($file)) : ?>
            <div class="source">
                <?= static::highlightFile($file, $line, 15); ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="container">
        <h2>
            Backtrace
        </h2>

        <?php foreach ($trace as $index => $row) : ?>

            <div>
                <p>
                    <!-- Trace info -->
                    <?php if (isset($row['file']) && is_file($row['file'])) :?>
                        <?php
                        if (isset($row['function']) && in_array($row['function'], ['include', 'include_once', 'require', 'require_once'], true)) {
                            echo esc($row['function'] . ' ' . clean_path($row['file']));
                        } else {
                            echo esc(clean_path($row['file']) . ' : ' . $row['line']);
                        }
                        ?>
                    <?php else: ?>
                        {PHP internal code}
                    <?php endif; ?>

                    <!-- Class/Method -->
                    <?php if (isset($row['class'])) : ?>
                        &nbsp;&nbsp;&mdash;&nbsp;&nbsp;<?= esc($row['class'] . $row['type'] . $row['function']) ?>
                        <?php if (! empty($row['args'])) : ?>
                            <?php $argsId = $errorId . 'args' . $index ?>
                            ( <a href="#" onclick="return toggle('<?= esc($argsId, 'attr') ?>');">arguments</a> )
                            <div class="args" id="<?= esc($argsId, 'attr') ?>">
                                <table cellspacing="0">

                                <?php
                                $params = null;
                                // Reflection by name is not available for closure function
                                if (substr($row['function'], -1) !== '}') {
                                    $mirror = isset($row['class']) ? new ReflectionMethod($row['class'], $row['function']) : new ReflectionFunction($row['function']);
                                    $params = $mirror->getParameters();
                                }

                                foreach ($row['args'] as $key => $value) : ?>
                                    <tr>
                                        <td><code><?= esc(isset($params[$key]) ? '$' . $params[$key]->name : "#{$key}") ?></code></td>
                                        <td><pre><?= esc(print_r($value, true)) ?></pre></td>
                                    </tr>
                                <?php endforeach ?>

                                </table>
                            </div>
                        <?php else : ?>
                            ()
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php if (! isset($row['class']) && isset($row['function'])) : ?>
                        &nbsp;&nbsp;&mdash;&nbsp;&nbsp;    <?= esc($row['function']) ?>()
                    <?php endif; ?>
                </p>

                <!-- Source? -->
                <?php if (isset($row['file']) && is_file($row['file']) && isset($row['class'])) : ?>
                    <div class="source">
                        <?= static::highlightFile($row['file'], $row['line']) ?>
                    </div>
                <?php endif; ?>
            </div>

        <?php endforeach; ?>

    </div> <!-- /container -->

    <div class="footer">
        <div class="container">

            <p>
                Displayed at <?= date('H:i:sa') ?> &mdash;
                PHP: <?= phpversion() ?>  &mdash;
                Aksara: <?= aksara('build_version') ?>
            </p>
            <p>
                <b>Aksara</b> is a <b>CodeIgniter</b> based automation platform
            </p>

        </div>
    </div>

</body>
</html>