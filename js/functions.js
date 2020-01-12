function selectedEventChange(e) {
    jQuery(function($) {
        var remainingT = $(e).find(":selected").data("remainingtickets");
        var eventID 			= $(e).find(":selected").data("eventid");
        var eventOccurrenceID	= $(e).find(":selected").data("eventoccurrenceid");
        $(e).attr("disabled", "disbaled");
        $("#wait_image").removeAttr("hidden");
        location.href = "?post_type=event&page=reservations&event_id=" + eventID + "&event_occurence_id=" + eventOccurrenceID
    });
}