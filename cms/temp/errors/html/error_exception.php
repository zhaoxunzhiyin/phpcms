<?php $error_id = uniqid('error', true); ?>
<!doctype html>
<html lang="zh-cn">
<head>
    <meta charset="UTF-8">
    <meta name="robots" content="noindex">

    <title><?= htmlspecialchars((string)$title, ENT_SUBSTITUTE, 'UTF-8') ?></title>
    <style type="text/css">
        <?= preg_replace('#[\r\n\t ]+#', ' ', file_get_contents(__DIR__.DIRECTORY_SEPARATOR.'debug.css')) ?>
        .source code {
            word-break:break-all;
            word-wrap:break-word;
        }
    </style>

    <script type="text/javascript">
        <?= file_get_contents(__DIR__.DIRECTORY_SEPARATOR.'debug.js') ?>
    </script>
</head>
<body onload="init()">

    <!-- Header -->
    <div class="header">
        <div class="container">
            <h2><?= htmlspecialchars((string)$title, ENT_SUBSTITUTE, 'UTF-8'), ($exception->getCode() ? ' #'.$exception->getCode() : '') ?></h2>
            <p>
                <?= $message ?>
                <a href="https://www.baidu.com/s?ie=UTF-8&wd=CMS%20<?= urlencode($title.' '.preg_replace('#\'.*\'|".*"#Us', '', $exception->getMessage())) ?>"
                   rel="noreferrer" target="_blank">搜索问题 &rarr;</a>
            </p>
            <?php if (strpos($title, 'mysqli') !== false) : ?><p>
                <?php pc_base::load_sys_class('model', '', 0); $model = new model(); echo $model->get_sql_query(); ?>
            </p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Source -->
    <div class="container">
        <?php if ($is_template) : ?>
            <?php foreach ($is_template as $index => $row) : ?>
                <p><b>模板文件：<?php echo $row['path'] ?></b> </p>
                <?php if ($line_template && is_file($row['path'])) : ?>
                    <div class="source">
                        <?= static::highlightFile($row['path'], $line_template, 15); ?>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?></div>
        <div class="container">
            <p><b>解析文件：<?php echo IS_DEV ? $file :  static::cleanPath($file, $line) ?></b> at line <b><?= $line ?></b></p>
        <?php else : ?>

            <p><b><?php echo IS_DEV ? $file :  static::cleanPath($file, $line) ?></b> at line <b><?= $line ?></b></p>
        <?php endif; ?>

        <?php if (is_file($file)) : ?>
            <div class="source">
                <?= static::highlightFile($file, $line, 15); ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="container">

        <ul class="tabs" id="tabs">
            <li><a href="#backtrace">Backtrace</a></li>
                <li><a href="#server">Server</a></li>
                <li><a href="#request">Request</a></li>
                <li><a href="#files">Files</a></li>
                <li><a href="#memory">Memory</a></li>
            </li>
        </ul>

        <div class="tab-content">

            <!-- Backtrace -->
            <div class="content" id="backtrace">

                <ol class="trace">
                <?php foreach ($trace as $index => $row) : ?>

                    <li>
                        <p>
                            <!-- Trace info -->
                            <?php if (isset($row['file']) && is_file($row['file'])) :?>
                                <?php
                                    if (isset($row['function']) && in_array($row['function'], ['include', 'include_once', 'require', 'require_once']))
                                    {
                                        echo $row['function'].' '. (IS_DEV ? $row['file'] : static::cleanPath($row['file']));
                                    }
                                    else
                                    {
                                        echo (IS_DEV ? $row['file'] :static::cleanPath($row['file'])).' : '.$row['line'];
                                    }
                                ?>
                            <?php else : ?>
                                {PHP internal code}
                            <?php endif; ?>

                            <!-- Class/Method -->
                            <?php if (isset($row['class'])) : ?>
                                &nbsp;&nbsp;&mdash;&nbsp;&nbsp;<?= $row['class'].$row['type'].$row['function'] ?>
                                <?php if (! empty($row['args'])) : ?>
                                    <?php $args_id = $error_id.'args'.$index ?>
                                    ( <a href="#" onclick="return toggle('<?= $args_id ?>');">arguments</a> )
                                    <div class="args" id="<?= $args_id ?>">
                                        <table cellspacing="0">

                                        <?php
                                        $params = null;
                                        // Reflection by name is not available for closure function
                                        if( substr( $row['function'], -1 ) !== '}' )
                                        {
                                            $mirror = isset( $row['class'] ) ? new \ReflectionMethod( $row['class'], $row['function'] ) : new \ReflectionFunction( $row['function'] );
                                            $params = $mirror->getParameters();
                                        }
                                        foreach ($row['args'] as $key => $value) : ?>
                                            <tr>
                                                <td><code><?= htmlspecialchars(isset($params[$key]) ? '$'.$params[$key]->name : "#$key", ENT_SUBSTITUTE, 'UTF-8') ?></code></td>
                                                <td><pre><?= print_r($value, true) ?></pre></td>
                                            </tr>
                                        <?php endforeach ?>

                                        </table>
                                    </div>
                                <?php else : ?>
                                    ()
                                <?php endif; ?>
                            <?php endif; ?>

                            <?php if (! isset($row['class']) && isset($row['function'])) : ?>
                                &nbsp;&nbsp;&mdash;&nbsp;&nbsp;    <?= $row['function'] ?>()
                            <?php endif; ?>
                        </p>

                        <!-- Source? -->
                        <?php if (isset($row['file']) && is_file($row['file']) &&  isset($row['class'])) : ?>
                            <div class="source">
                                <?= static::highlightFile($row['file'], $row['line']) ?>
                            </div>
                        <?php endif; ?>
                    </li>

                <?php endforeach; ?>
                </ol>

            </div>

            <!-- Server -->
            <div class="content" id="server">
                <?php foreach (['_SERVER', '_SESSION'] as $var) : ?>
                    <?php if (empty($GLOBALS[$var]) || ! is_array($GLOBALS[$var])) continue; ?>

                    <h3>$<?= $var ?></h3>

                    <table>
                        <thead>
                            <tr>
                                <th>Key</th>
                                <th>Value</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($GLOBALS[$var] as $key => $value) : ?>
                            <tr>
                                <td><?= htmlspecialchars((string)$key, ENT_IGNORE, 'UTF-8') ?></td>
                                <td>
                                    <?php if (is_string($value)) : ?>
                                        <?= htmlspecialchars((string)$value, ENT_SUBSTITUTE, 'UTF-8') ?>
                                    <?php else: ?>
                                        <?= '<pre>'.print_r($value, true) ?>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>

                <?php endforeach ?>

                <!-- Constants -->
                <?php $constants = get_defined_constants(true); ?>
                <?php if (! empty($constants['user'])) : ?>
                    <h3>Constants</h3>

                    <table>
                        <thead>
                            <tr>
                                <th>Key</th>
                                <th>Value</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($constants['user'] as $key => $value) : ?>
                            <tr>
                                <td><?= htmlspecialchars((string)$key, ENT_IGNORE, 'UTF-8') ?></td>
                                <td>
                                    <?php if (!is_array($value) && ! is_object($value)) : ?>
                                        <?= htmlspecialchars((string)$value, ENT_SUBSTITUTE, 'UTF-8') ?>
                                    <?php else: ?>
                                        <?= '<pre>'.print_r($value, true) ?>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>

            <!-- Request -->
            <div class="content" id="request">

                <table>
                    <tbody>
                    <tr>
                        <td style="width: 10em">Path</td>
                        <td><?= FC_NOW_URL ?></td>
                    </tr>
                    <tr>
                        <td>HTTP Method</td>
                        <td><?= $_SERVER['REQUEST_METHOD'] ?></td>
                    </tr>
                    <tr>
                        <td>IP Address</td>
                        <td><?= ip() ?></td>
                    </tr>
                    <tr>
                        <td style="width: 10em">Is AJAX?</td>
                        <td><?= IS_AJAX ? 'yes' : 'no' ?></td>
                    </tr>
                    <tr>
                        <td>Is CLI?</td>
                        <td><?= is_cli() ? 'yes' : 'no' ?></td>
                    </tr>
                    <tr>
                        <td>User Agent</td>
                        <td><?= $_SERVER['HTTP_USER_AGENT'] ?></td>
                    </tr>

                    </tbody>
                </table>


                <?php $empty = true; ?>
                <?php foreach (['_GET', '_POST', '_COOKIE'] as $var) : ?>
                    <?php if (empty($GLOBALS[$var]) || ! is_array($GLOBALS[$var])) continue; ?>

                    <?php $empty = false; ?>

                    <h3>$<?= $var ?></h3>

                    <table style="width: 100%">
                        <thead>
                            <tr>
                                <th>Key</th>
                                <th>Value</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($GLOBALS[$var] as $key => $value) : ?>
                            <tr>
                                <td><?= htmlspecialchars((string)$key, ENT_IGNORE, 'UTF-8') ?></td>
                                <td>
                                    <?php if (!is_array($value) && ! is_object($value) && $value) : ?>
                                        <?= htmlspecialchars((string)$value, ENT_SUBSTITUTE, 'UTF-8') ?>
                                    <?php else: ?>
                                        <?= '<pre>'.print_r($value, true) ?>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>

                <?php endforeach ?>

                <?php if ($empty) : ?>

                    <div class="alert">
                        No $_GET, $_POST, or $_COOKIE Information to show.
                    </div>

                <?php endif; ?>

                <?php $headers = getallheaders(); ?>
                <?php if (! empty($headers)) : ?>

                    <h3>Headers</h3>

                    <table>
                        <thead>
                            <tr>
                                <th>Header</th>
                                <th>Value</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($headers as $name => $value) : ?>
                            <?php if (empty($value)) continue; ?>
                                <tr>
                                    <td><?= $name ?></td>
                                    <td><?= $value ?></td>
                                </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>

                <?php endif; ?>

            </div>

            <!-- Files -->
            <div class="content" id="files">
                <?php $files = get_included_files(); ?>

                <ol>
                <?php foreach ($files as $file) :?>
                    <li><?= htmlspecialchars( IS_DEV ? $file :static::cleanPath($file), ENT_SUBSTITUTE, 'UTF-8') ?></li>
                <?php endforeach ?>
                </ol>
            </div>

            <!-- Memory -->
            <div class="content" id="memory">

                <table>
                    <tbody>
                        <tr>
                            <td>Memory Usage</td>
                            <td><?= static::describeMemory(memory_get_usage(true)) ?></td>
                        </tr>
                        <tr>
                            <td style="width: 12em">Peak Memory Usage:</td>
                            <td><?= static::describeMemory(memory_get_peak_usage(true)) ?></td>
                        </tr>
                        <tr>
                            <td>Memory Limit:</td>
                            <td><?= ini_get('memory_limit') ?></td>
                        </tr>
                    </tbody>
                </table>

            </div>

        </div>  <!-- /tab-content -->

    </div> <!-- /container -->

    <div class="footer">
        <div class="container">

            <p>
                Displayed at <?= date('H:i:sa') ?> &mdash;
                PHP: <?= phpversion() ?> &mdash;
                CMS: <?= get_pc_version() ?>
            </p>

        </div>
    </div>

</body>
</html>
