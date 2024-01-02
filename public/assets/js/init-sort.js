$(document).ready(function() {
    
    wrapper = document.getElementById('results--row');
    
    new Sortable(wrapper, {
        multiDrag: true, // Enable multi-drag
	    selectedClass: 'sort-drag', // The class applied to the selected items
        fallbackTolerance: 3, // So that we can select items on mobile
        animation: 400,
        handle: '.handle',
    });
});