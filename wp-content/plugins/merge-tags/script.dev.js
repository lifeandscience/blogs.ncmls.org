jQuery(document).ready(function($) {
	var vars = window.mergeTagsL10n;

	$('.actions select').each(function(i) {
		var $self = $(this);

		var extra = i ? '2' : '';
		var $field = $('<span> ' + vars.to_tag + ': <input name="bulk_to_tag' + extra + '" type="text" size="20"></span>').css('display', 'none');

		$self.after($field);

		$self.find('option:first').after('<option value="bulk-merge-tag">' + vars.action + '</option>');

		$self.change(function() {
			if ( $self.val() == 'bulk-merge-tag' )
				$field.css('display', 'inline').find('input').focus();
			else
				$field.css('display', 'none');
		});
	});
});
