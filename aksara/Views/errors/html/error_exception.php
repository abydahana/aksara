<?php $error_id = uniqid('error', true); ?>
<!doctype html>
<html>
<head>
	<meta charset="UTF-8">
	<meta name="robots" content="noindex">

	<title><?= htmlspecialchars($title, ENT_SUBSTITUTE, 'UTF-8') ?></title>
	<style type="text/css">
		<?= preg_replace('#[\r\n\t ]+#', ' ', file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'debug.css')) ?>
	</style>

	<script type="text/javascript">
		<?= file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'debug.js') ?>
	</script>
</head>
<body onload="init()">

	<!-- Header -->
	<div class="header">
		<div class="container">
			<h1><?= htmlspecialchars($title, ENT_SUBSTITUTE, 'UTF-8'), ($exception->getCode() ? ' #' . $exception->getCode() : '') ?></h1>
			<p>
				<?= $exception->getMessage() ?>
				<a href="https://www.google.com/search?q=<?= urlencode($title . ' ' . preg_replace('#\'.*\'|".*"#Us', '', $exception->getMessage())) ?>"
				   rel="noreferrer" target="_blank">search &rarr;</a>
			</p>
		</div>
	</div>

	<!-- Source -->
	<div class="container">
		<p><b><?= static::cleanPath($file, $line) ?></b> at line <b><?= $line ?></b></p>

		<?php if (is_file($file)) : ?>
			<div class="source">
				<?= static::highlightFile($file, $line, 15); ?>
			</div>
		<?php endif; ?>
	</div>

	<div class="container">
	
		<!-- Backtrace -->
		<?php foreach ($trace as $index => $row) : ?>
		
			<p>
				<!-- Trace info -->
				<?php if (isset($row['file']) && is_file($row['file'])) :?>
					<?php
					if (isset($row['function']) && in_array($row['function'], ['include', 'include_once', 'require', 'require_once']))
						{
						echo $row['function'] . ' ' . static::cleanPath($row['file']);
					}
					else
						{
						echo static::cleanPath($row['file']) . ' : ' . $row['line'];
					}
					?>
				<?php else : ?>
					{PHP internal code}
				<?php endif; ?>

				<!-- Class/Method -->
				<?php if (isset($row['class'])) : ?>
					&nbsp;&nbsp;&mdash;&nbsp;&nbsp;<?= $row['class'] . $row['type'] . $row['function'] ?>
					<?php if (! empty($row['args'])) : ?>
						<?php $args_id = $error_id . 'args' . $index ?>
						( <a href="#" onclick="return toggle('<?= $args_id ?>');">arguments</a> )
						<div class="args" id="<?= $args_id ?>">
							<table cellspacing="0">

							<?php
							$params = null;
							// Reflection by name is not available for closure function
							if (substr( $row['function'], -1 ) !== '}')
							{
								$mirror = isset( $row['class'] ) ? new \ReflectionMethod( $row['class'], $row['function'] ) : new \ReflectionFunction( $row['function'] );
								$params = $mirror->getParameters();
							}
							foreach ($row['args'] as $key => $value) : ?>
								<tr>
									<td><code><?= htmlspecialchars(isset($params[$key]) ? '$' . $params[$key]->name : "#$key", ENT_SUBSTITUTE, 'UTF-8') ?></code></td>
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
					&nbsp;&nbsp;&mdash;&nbsp;&nbsp;	<?= $row['function'] ?>()
				<?php endif; ?>
			</p>

			<!-- Source? -->
			<?php if (isset($row['file']) && is_file($row['file']) &&  isset($row['class'])) : ?>
				<div class="source">
					<?= static::highlightFile($row['file'], $row['line']) ?>
				</div>
			<?php endif; ?>
			<br />
		<?php endforeach; ?>

	</div> <!-- /container -->

	<div class="footer">
		<div class="container">

			<p>
				Displayed at <?= date('H:i:sa') ?> &mdash;
				PHP: <?= phpversion() ?>  &mdash;
				Aksara: <?= aksara('build_version') ?>
			</p>

		</div>
	</div>

</body>
</html>
