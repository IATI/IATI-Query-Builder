$(function() {
  var QueryBuilder = QueryBuilder || {};
  QueryBuilder.LinkGen = QueryBuilder.LinkGen || {};
  QueryBuilder.Preview = QueryBuilder.Preview || {};
  QueryBuilder.Plugins = QueryBuilder.Plugins || {};
  QueryBuilder.UI = QueryBuilder.UI || {};

  /*
    Obtain the format.
  */
  QueryBuilder.LinkGen.getFormat = function(){
    return $('#qb-format-list input:checked').val();
  };

  /*
    Obtain the grouping.
  */
  QueryBuilder.LinkGen.getGrouping = function(){
    var grouping = $('#qb-grouping-list input:checked').val();

    if(grouping === 'summary'){
      return '';
      } else {
        return '/' + grouping;
    }
  };

  /*
    Obtain the sample size
  */
  QueryBuilder.LinkGen.getSampleSize = function(){
    var sampleSize = $('#qb-sample-size-list input:checked').val();

    if(sampleSize === '50 rows'){
      return '';
    } else {
      return 'stream=True'
    }
  };

  /*
    Obtain the calendar date
  */
  QueryBuilder.LinkGen.getCalendarDate = function(id){
    var calendarDate = $('#' + id).val();

    if(calendarDate.trim() === ''){
      return '';
    } else {
      return id + '=' + calendarDate;
    }
  };

  /*
    Obtain info for a multi-select.
  */
  QueryBuilder.LinkGen.getMultiSelectInfo = function(name){
    var separator = '%7C';
    var multiSelect = $('[name="' + name + '[]"]');
    var selectedOptions = $('[name="' + name + '[]"] option:checked');
    var values = [];

    // have to use forEach because querySelectorAll is a NodeList which doesn't have a `map()` function
    selectedOptions.each(function(){
        var value = $(this).val();

      // do not include the 'None' values
      if(value !== ''){
        values.push(value);
      }
    });

    if(values.length === 0){
      return '';
    }

    return name + '=' + values.join(separator);
  };

  /*
    Convert an array of query string sections into a query string.
  */
  QueryBuilder.LinkGen.createQueryString = function(sections){
    var filteredSections = sections.filter(function(section){
      return section !== '';
    });

    if(filteredSections.length === 0){
      return '';
    }

    return '?' + filteredSections.join('&');
  };

  /*
    Generate the link from the page.
  */
  QueryBuilder.LinkGen.createLink = function(fileType){
    var baseURL = 'http://datastore.iatistandard.org/api/1/access/';
    var format = QueryBuilder.LinkGen.getFormat();
    var grouping = QueryBuilder.LinkGen.getGrouping();
    var reportingOrgs = QueryBuilder.LinkGen.getMultiSelectInfo('reporting-org');
    var reportingOrgTypes = QueryBuilder.LinkGen.getMultiSelectInfo('reporting-org.type');
    var sectors = QueryBuilder.LinkGen.getMultiSelectInfo('sector');
    var recipientCountries = QueryBuilder.LinkGen.getMultiSelectInfo('recipient-country');
    var recipientRegions = QueryBuilder.LinkGen.getMultiSelectInfo('recipient-region');
    var startDateLT = QueryBuilder.LinkGen.getCalendarDate('start-date__lt');
    var startDateGT = QueryBuilder.LinkGen.getCalendarDate('start-date__gt');
    var endDateLT = QueryBuilder.LinkGen.getCalendarDate('end-date__lt');
    var endDateGT = QueryBuilder.LinkGen.getCalendarDate('end-date__gt');
    var transactionProviderOrgs = QueryBuilder.LinkGen.getMultiSelectInfo('transaction_provider-org');
    var participatingOrgs = QueryBuilder.LinkGen.getMultiSelectInfo('participating-org');
    var sampleSize = QueryBuilder.LinkGen.getSampleSize();
    var queryString = QueryBuilder.LinkGen.createQueryString([
      reportingOrgs,
      reportingOrgTypes,
      sectors,
      recipientCountries,
      recipientRegions,
      startDateLT,
      startDateGT,
      endDateLT,
      endDateGT,
      transactionProviderOrgs,
      participatingOrgs,
      sampleSize
    ]);

    fileType = fileType || '.csv';

    return baseURL + format + grouping + fileType + queryString;
  };

  /*
    Update the link displayed on the page.
  */
  QueryBuilder.LinkGen.updateLink = function(){
    var linkElement = $('#query-link');
    var linkLocation = QueryBuilder.LinkGen.createLink();

    // update the link
    linkElement.attr('href', linkLocation);
    linkElement.text(linkLocation);
  };

  /*
    Set the event listeners for the generated link.
  */
  QueryBuilder.LinkGen.SetupEventListeners = function(){
    var formElements = $('select, input[type=text], input[type=radio]');

    formElements.change(QueryBuilder.LinkGen.updateLink);
    };

  /*
    Initialise the client-side link generation.
  */
  QueryBuilder.LinkGen.Init = function(){
    $('#js-query-link').show();

    QueryBuilder.LinkGen.SetupEventListeners();
    QueryBuilder.LinkGen.updateLink();
  };

  /*
    Generate the link to the Preview tool.
  */
  QueryBuilder.Preview.createLink = function(){
    var unescapedLink = QueryBuilder.LinkGen.createLink('.xml');
    var previewBaseURL = 'http://preview.iatistandard.org/index.php?url=';

    // ensure it's only a Preview and not the full set of data being viewed
    var sampleSize = QueryBuilder.LinkGen.getSampleSize();

    if(sampleSize.length > 0){
      unescapedLink = unescapedLink.replace(sampleSize, '');
    }

    return previewBaseURL + encodeURIComponent(unescapedLink);
  };

  /*
    Update the integration with the Preview tool.
  */
  QueryBuilder.Preview.updateLink = function(){
    var previewButtonElement = $('#preview-link');

    previewButtonElement.attr('href', QueryBuilder.Preview.createLink());
    QueryBuilder.Preview.updateVisibility();
  };

  /*
    Changes the Preview link visibility based on whether the data can be viewed using the Preview Tool.
  */
  QueryBuilder.Preview.updateVisibility = function(){
    var format = QueryBuilder.LinkGen.getFormat();
    var grouping = QueryBuilder.LinkGen.getGrouping();
    var canPreview = (format === 'activity') && (grouping === '');
    var previewWrapper = $('#preview-link-wrapper');

    if(canPreview){
      previewWrapper.show();
    } else {
      previewWrapper.hide();
    }
  };

  /*
    Set the event listeners for the preview integration
  */
  QueryBuilder.Preview.SetupEventListeners = function(){
    var formElements = $('select, input[type=text], input[type=radio]');

    formElements.change(QueryBuilder.Preview.updateLink);
  };

  /*
    Initialise the preview integration.
  */
  QueryBuilder.Preview.Init = function(){
    QueryBuilder.Preview.SetupEventListeners();
    QueryBuilder.Preview.updateLink();
  };

  /*
    Initialise plugins and 3rd party code.
  */
  QueryBuilder.Plugins.Init = function(){
    // remove '- None -' values from selects (they don't play nicely with chosen)
    $('select option[value=""]').remove();

    // initialise chosen
    $(".chosen-select").chosen();

    $('#reset').attr('type', 'reset');
    $('#qb-form').on('reset', QueryBuilder.UI.resetSelection);

    // init tooltips
    $('[data-toggle="tooltip"]').tooltip();
  };

  /*
    Reset the values selected in the form.
  */
  QueryBuilder.UI.resetSelection = function(){
    setTimeout(function(){
      $(window).scrollTop(0);

      $(".chosen-select").trigger("chosen:updated");

      QueryBuilder.LinkGen.updateLink();
      QueryBuilder.Preview.updateLink();
    });
  };

  /*
    Initialise the Query Builder JS.
  */
  QueryBuilder.Init = function(){
    QueryBuilder.LinkGen.Init();
    QueryBuilder.Preview.Init();
    QueryBuilder.Plugins.Init();
  };

  QueryBuilder.Init();
});
