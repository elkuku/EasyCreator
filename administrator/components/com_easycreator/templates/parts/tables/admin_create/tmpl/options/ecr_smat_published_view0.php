<?php
$lists['published'] = JHTML::_('select.booleanlist', 'published', 'class="inputbox"', $item->published);
$this->assignRef('lists', $lists);
