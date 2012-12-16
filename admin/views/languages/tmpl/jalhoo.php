<?php
echo 'HI';
?>
<style>
div.jlpOptions {
padding-top: 0.7em;
width: 100%;
}
div.jlpOptions div {
float: left;
margin-left: 0.5em;
}
</style>

<div class="jlpOptions">
	<div>
		Language<br />
        <?php echo $this->lists['language']; ?>
	</div>
	<div>
		Extension<br />
        <?php //#echo $this->lists['components']; ?>
	</div>
	<div>
		Scope<br />
        <?php echo $this->lists['scopes']; ?>
	</div>
	<div>
		sub scope<br />
        <?php //#echo $this->lists['subScope']; ?>
	</div>
	<div>
		<?php echo $this->checks->get('includeCoreLanguage'); ?>
    </div>
</div>
<div style="clear: both"></div><div class="jlpOptions" style="background-color: #ccff99;">
	<div>
		IN Format<br />
        <?php echo $this->lists['langFormatIn']; ?>
	</div>
</div>
<div style="clear: both"></div>
<div class="jlpOptions" style="background-color: #ffc;">
	<div>
		OUT Format<br />
        <?php echo $this->lists['langFormatOut']; ?>
	</div>
    <div>
		<?php echo $this->checks->get('includeLineNumbers'); ?>
	</div>
	<div>
        <?php echo $this->checks->get('markFuzzy'); ?>
        <br />
        <?php echo $this->checks->get('markKeyDiffers'); ?>
    </div>
</div>
	<div style="width: 200px; float: left; margin-left: 1em;">
        <b>Exclude dirs:</b>
        <br />
		<div id="tree_container"></div>
	</div>
<div style="clear: both"></div>
<div class="jlpOptions" style="background-color: #cce5ff;">
<div class="ecr_button" onclick="submitform('jalhoo');">PU</div>
<!--
<input type="submit" name="task" value="test" value="TEST" />
<input type="submit" name="task" value="write" value="WRITE" />
 -->
</div>
<div style="clear: both"></div>

<input type="hidden" id="excludeDirs" name="excludeDirs" value="<?php echo $this->excludeDirs; ?>" />
<?php if($this->langFormatOut) : ?>
<textarea style="font-size: 12px; height: 300px; width: 100%; overflow: auto;
background-color: #fff; border: 2px dashed gray; padding: 0.5em;">
<?php


    //-- #echo $this->loadTemplate($this->langFormatOut);
echo $this->parser->generate($this->checker, $this->buildOpts);

?>
</textarea>
<?php endif;
