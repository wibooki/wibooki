var $collectionHolder;

// setup an "add a tag" link
var $addItinerateLink = $('<a href="#" class="add_itinerate_link">Añadir itinerario</a>');
var $newLinkLi = $('<div class="col-sm-12"></div>').append($addItinerateLink);

jQuery(document).ready(function () {
    // Get the ul that holds the collection of tags
    $collectionHolder = $('div.itineraries');

    // add the "add a tag" anchor and li to the tags ul
    $collectionHolder.append($newLinkLi);

    // count the current form inputs we have (e.g. 2), use that as the new
    // index when inserting a new item (e.g. 2)
    $collectionHolder.data('index', $collectionHolder.find(':input').length);

    $addItinerateLink.on('click', function (e) {
        // prevent the link from creating a "#" on the URL
        e.preventDefault();

        // add a new tag form (see next code block)
        addItineraryForm($collectionHolder, $newLinkLi);
    });
});

function addItineraryForm($collectionHolder, $newLinkLi) {
    // Get the data-prototype explained earlier
    var prototype = $collectionHolder.data('prototype');

    // get the new index
    var index = $collectionHolder.data('index');

    // Replace '__name__' in the prototype's HTML to
    // instead be a number based on how many items we have
    var newForm = prototype.replace(/__name__/g, index);

    // increase the index with one for the next item
    $collectionHolder.data('index', index + 1);

    // Display the form in the page in an li, before the "Add a tag" link li
    var $newFormLi = $('<div class="col-sm-12"></div>').append(newForm);

    $newFormLi.append('<a href="#" class="remove-tag">Eliminar Itinerario</a>');

    $newLinkLi.before($newFormLi);

    $('.remove-tag').click(function (e) {
        e.preventDefault();

        $(this).parent().remove();

        return false;
    });
}