<tr class="opt-<?=$opt['id']?> opt" data-id="<?=$opt['id']?>">
    <td><i class="mdi mdi-menu text-gray cursor-pointer"></i>&nbsp;&nbsp;<span><?=$opt['label']?></span></td>
    <td align="right">
        <strong class="text-red"><?=$opt['value']==0?"":"+ ".Currency::parseCurrencyFormat($opt['value'],DEFAULT_CURRENCY)?></strong>
        &nbsp;&nbsp;&nbsp;<a href="#" class="remove-opt" data-id="<?=$opt['id']?>"><i class="mdi mdi-delete text-red"></i>&nbsp;&nbsp;</a>
    </td>
</tr>