<table class="ls">
<thead>
	<tr>
		<td>Filename</td>
		<td>Filetype</td>
		<td>Filesize</td>
		<td>Last Change</td>
	</tr>
</thead>
<tbody>

<?php
if (!is_array($data['files'])) { 
?>
<tr>
    <td colspan="4">** Directory content is hidden **<img src="<?=URI_BASE?>img/locked.png" alt="locked" width="22px"/></td>
</tr>
<?php
} else {
    foreach ($data['files'] as $fname => $file)
{
?>
<tr>
    <td>
        <a href="<?=$file['href']?>"><?=$fname?></a> 
        <? if ($file['protected'] && $file['locked']) { ?>
            <img src="<?=URI_BASE?>img/locked.png" alt="locked" width="22px" />
        <? } if ($file['protected'] && !$file['locked']) { ?>
            <img src="<?=URI_BASE?>img/unlocked.png" alt="unlocked" width="22px" />
        <?php } ?>
    </td>
    <td><?=$file['type']?></td>
    <td><?=$file['size']?> KiB</td>
    <td><?=date(DATE_FORMAT, $file['time'])?></td>
</tr>
<?php
}
}
?>
</tbody>
</table>
