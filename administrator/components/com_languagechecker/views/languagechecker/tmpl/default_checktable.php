<?php if( ! $this->comPath) return; ?>

<h1 style="text-align: center;">Strings and Translations</h1>

<?php if( ! count($this->strings)) : ?>
    <h3>Nothing found..</h3>
    <?php return; ?>
<?php endif; ?>

<p><?php echo sprintf('<b>%d</b> Strings found in code.', count($this->strings)); ?>
</p>
<b style="color: blue;">CP</b>: <tt><?php echo str_replace(JPATH_ROOT, 'JROOT', $this->comPath); ?></tt>
<table class="adminlist">
	<tr>
		<th>String to translate</th>
		<th>Files<br />
	        <?php echo $this->lists['filefilter']; ?>
		</th>
		<th>Status<br />
            <?php echo $this->lists['statusfilter']; ?><br />
            <span style="color: green;">component</span> / <span style="color: orange;">core</span>
		</th>
		<th>Translated string en-GB</th>
	</tr>
	<?php
	$xPath = '';
	$k = 0;
	foreach ($this->strings as $key => $string) :
	if( ! $string->display) continue;
	if($this->statusFilter == 'untranslated' && $string->isTranslated) continue;
	?>
		<tr class="row<?php echo $k; ?>">
			<td><?php echo $key; ?></td>
			<td>
				<?php
				foreach ($string->files as $path => $lineNums)
				{
				    $dPath = str_replace($this->comPath, '<b style="color: blue;">CP</b>', $path);

				    if($dPath != $xPath) :
				        echo "<tt>$dPath</tt>";
				        $xPath = $dPath;
				    else :
				        echo '&nbsp;|_';
				    endif;

				    echo ' ('.implode(', ', $lineNums).')<br />';
				}?>
			</td>
			<td>
			<?php
			if($string->isTranslated) :
			    $color =($string->isTranslatedInCore) ? 'orange' : 'green';
			    echo '<span style="color: '.$color.';">'.'Found'.'</span>';

			    echo ' ('.implode(', ', $string->iniLines).')';
			    if(count($string->iniLines) > 1) :

			        echo '<b style="color: red;"> ***'.'DOUBLE'.'***</b>';
			    endif;
			else :
			    echo '<span style="color: red;">'.'Not Found'.'</span>';
			endif;
			?>
			</td>
			<td>
			<?php
			if($string->isTranslated)
			{
			    if(strlen($string->translation) > 40)
			    {
			        echo htmlspecialchars(substr($string->translation, 0, 40)).'...';
			        echo '<b class="hasTip" title="'.htmlspecialchars($string->translation).'">more</b>';
			    }
			    else
              {
    			    echo htmlspecialchars($string->translation);
			    }
			}
			?>
			</td>
		</tr>
		<?php $k = 1 - $k; ?>
	<?php endforeach;?>
</table>
<?php if(count($this->strangeTHINGS)) : ?>
	<h1>Strange things found on the way..</h1>
    <ol>
    <?php foreach ($this->strangeTHINGS as $THING) : ?>
		<li><?php echo $THING; ?></li>
    <?php endforeach; ?>
    </ol>
<?php endif; ?>
<h1>Strings that have been found in the language file with no <em>appearent</em> use</h1>
Most might be false positives - Please help =;)

<ol>
<?php
foreach ($this->translations as $key => $translation)
{
    if($translation->isCore) continue;

	if( ! $translation->isUsed)
	{
	    echo '<li>'.$key.' ('.implode(', ', $translation->lines).')</li>';
	}
}?>
</ol>
<h1>Loaded language files</h1>
<ol>
<?php foreach ($this->loadedLanguageFiles as $file)
{
    echo '<li>'.str_replace(JPATH_ROOT, 'JROOT', $file).'</li>';
}?>
</ol>
<?php
