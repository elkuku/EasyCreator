				<td class='order'>
				<span><?php echo $this->pagination->orderUpIcon($i, true, 'orderup', 'Move Up', $ordering); ?></span>
				<span><?php echo $this->pagination->orderDownIcon($i, $n, true, 'orderdown', 'Move Down', $ordering); ?></span>
				<?php $disabled = $ordering ?  '' : 'disabled=\"disabled\"'; ?>
				<input type='text' name='order[]' size='5' value='<?php echo $row->ordering; ?>'
				<?php echo $disabled; ?> class='text_area' style='text-align: center' />