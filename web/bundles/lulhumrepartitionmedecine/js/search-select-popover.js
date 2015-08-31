function activateSearchSelectPopover() {
    $('[data-toggle="popover"]').popover({
	content : function() {
	    var $inputgroup = $(this).parent().parent();
	    var $content = $('<div></div>');
	    var $select = $inputgroup.children("select");
	    var $search = $('<input type="text" data-lulhumsearchfor="'+$(this).attr('id')+'" data-select="'+$select.attr('id')+'" placeholder="Rechercher..." />');
	    $content.append($search);				
	    var $optgroups = $select.children("optgroup");
	    if($optgroups.length > 0) {
		$selOpts = $('<select data-lulhumsearchfor="'+$(this).attr('id')+'" class="form-control"></select>');
		$selOpts.append($('<option value="" selected="selected">Aucun</option>'));
		$optgroups.each(function() {
		    var optgroup = $(this).attr('label');
		    $selOpts.append($('<option value="'+optgroup+'">'+optgroup+'</option>'));
		});
		$content.append($selOpts);
	    }				
	    
	    return $content.html();
	},
    });

    $('[data-toggle="popover"]').on('shown.bs.popover', function() {
	var $search =  $('input[data-lulhumsearchfor="'+$(this).attr('id')+'"]');
	var $select = $('select#'+$search.attr('data-select'));
	var $optgroups = $('select[data-lulhumsearchfor="'+$(this).attr('id')+'"]');
	var optgroup = '';
	if($optgroups.length > 0) {
	    $optgroups.change(function() {
		optgroup = $optgroups.val();
	    });
	}
	$search.keyup(function() {
	    $select.children('option:selected').prop('selected', false);
	    if(optgroup === '') {
		$select.children('option:contains("'+$search.val()+'"):first').prop('selected', true);
	    }
	    else {
		$select.children('optgroup[label="'+optgroup+'"]').children('option:contains("'+$search.val()+'"):first').prop('selected', true);
	    }
	});
    });
}
