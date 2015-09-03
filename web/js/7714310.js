function easyRequirementInput($localContainer, proposalId, formPath) {
    var $select = $localContainer.find('select').first();
    var $input = $localContainer.find('input').first();
    var $inputLabel = $localContainer.find('label[for="'+$input.attr('id')+'"]');
    $inputLabel.html('Chargement...');
    $input.attr('disabled', true);
    $.ajax({
	type: "POST",
	url: formPath,
	data: {
	    'paramType': $select.val(),
	    'proposal': proposalId
	},
	cache: false,
	success: function(data) {
	    $input.popover({
		html: true,
		placement: 'top',
		trigger: 'click focus',
	    }).on('shown.bs.popover', function() {
		$localPopover = $localContainer.find('div.requirement-popover');
		$localPopover.find('a').click(function(e) {
		    if($localPopover.find('[type!="hidden"]:input').size() == 1) {
			var $result = $localPopover.find('[type!="hidden"]:input').val();
		    }
		    else {
			var $result = '{'
			$localPopover.find('[type!="hidden"]:input').each(function() { $result+=$(this).val()+',' });
			$result += '}';
		    }
		    $input.popover('hide');
		    $input.val($result);
		    e.preventDefault();
		    return false;
		});
	    });
	    $input.attr('data-content', data);
	    if(data != '') {
		$input.attr('readonly', true);
	    }
	    else {
		$input.attr('readonly', false);
	    }
	    $inputLabel.html('Paramètre');
	    $input.attr('disabled', false);
	}
    });
    $select.change(function() {
	$inputLabel.html('Chargement...');
	$input.attr('disabled', true);
	$input.popover('hide');
	$.ajax({
	    type: "POST",
	    url: formPath,
	    data: {
		'paramType': $select.val(),
		'proposal': proposalId
	    },
	    cache: false,
	    success: function(data) {
		$input.attr('data-content', data);
		$input.val('');
		if(data != '') {
		    $input.attr('readonly', true);
		}
		else {
		    $input.attr('readonly', false);
		}
		$inputLabel.html('Paramètre');
		$input.attr('disabled', false);
	    }
	});
    });
}

function manageRequirements($requirementsContainer, formPath, proposal, initial) {
    
    var index = 0;
    
    $requirementsContainer.children('div').each(function() {
	addDeleteLink($(this));
	$(this).find('div.panel-heading').children('label').html('Contrainte n°' + (index+1));
	easyRequirementInput($(this), proposal, formPath);
	index++;
    });
    
    var $addRequirementLink = $('<a href="#" id="add_requirement" class="btn btn-success" style="margin-right:10px">Ajouter une contrainte</a>');
    $addRequirementLink.insertBefore($('button[type="submit"]'));
    
    $addRequirementLink.click(function(e) {
	addRequirement($requirementsContainer);
	e.preventDefault();
	return false;
    });
    
    for(i = 0; i < initial; i++) {
	addRequirement($requirementsContainer);
    }
    
    function addRequirement($container) {
	var $prototype = $($container.attr('data-prototype').replace(/__name__label__/g, 'Contrainte n°' + (index+1))
			   .replace(/__name__/g, index));
	easyRequirementInput($prototype, proposal, formPath);
	addDeleteLink($prototype);
	$prototype.insertBefore($addRequirementLink);
	index++;
    }
    
    function addDeleteLink($prototype) {
	$deleteLink = $('<a href="#" class="btn btn-danger">Supprimer</a>');
	$prototype.children('div.panel-footer').append($deleteLink);
	$deleteLink.click(function(e) {
	    $prototype.remove();
	    e.preventDefault();
	    return false;
	});
    }
}
