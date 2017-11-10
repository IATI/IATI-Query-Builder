"""Helper functions for form."""
import csv
import json
import iati


CACHEFILE = 'groups_cache_dc.json'


def csv_to_list(path):
    """Add non-title rows of CSV file to list.

    Note:
        This replaces WET functions 'get_regions', and 'get_org_types' in original code.

    Todo:
        Test for exceptions.
        Return list instead of dict.

    """
    csv_list = list()

    try:
        with open(path, 'r') as csvfile:
            csv_file = csvfile.read()
            data = csv_file.splitlines()
            data.pop(0)
            for value in data:
                csv_list.append(value)
        return csv_list
    except:
        raise ValueError("File not found.")


def build_sanitised_multi_select_values(path, sanitized_values):
    """Check values of a list are permitted and add to multi-select list.

    Todo:
        Rename this function to describe actual purpose. Maybe something like `filter_permitted_values`.
        Flask has build in defences against XSS attacks so is this even needed?

    """
    values = list()
    allowed_values = csv_to_list(path)

    if (sanitized_values != list()):
        for requested_value in sanitized_values:
            if (requested_value in allowed_values) and (requested_value is not None):
                values.append(requested_value)

    if (values == list()):
        return None
    return values


def sort_dict_by_keys(dictionary_to_sort):
    """Sort a dict by its keys."""
    sorted_dict = dict()
    for key in sorted(dictionary_to_sort.keys(), key=str.lower()):
        sorted_dict[key] = dictionary_to_sort[key]
    return sorted_dict


def reporting_orgs(cache_file=CACHEFILE):
    """Return sorted dictionary for organisations."""
    reporting_orgs = dict()
    excluded_ids = ['To be confirmed.']

    with open(cache_file) as c_file:
        groups = json.load(c_file)

        for key, value in groups.items():
            if value['packages'] != list():
                publisher_iati_id = value['extras']['publisher_iati_id']
                if publisher_iati_id not in excluded_ids:
                    reporting_orgs[value['display_name']] = publisher_iati_id

    sorted_orgs = sort_dict_by_keys(reporting_orgs)

    return sorted_orgs


def get_codelist_values(codelist_name):
    """Format list for multiselect from a given codelist.

    Todo:
        More research into built in defences against XSS attack.
        Refactor with get_countries to make DRY.

    """
    codelist_values = dict()
    codelist = iati.default.codelist(codelist_name)

    for code in codelist.codes:
        name = code.name
        codelist_values[code.value] = code.name

    sorted_codelist_values = sort_dict_by_keys(codelist_values)

    return sorted_codelist_values


def get_countries():
    """Format country list for multiselect.

    Todo:
        More research into built in defences against XSS attack.
        Need to refactor with get_codelist_values to make DRY.

    """
    countries = dict()
    country_codelist = iati.default.codelist('Country')

    for country_code in country_codelist.codes:
        country_name = country_code.name.title()
        countries[country_name] = country_code.value

    sorted_countries = sort_dict_by_keys(countries)

    return sorted_countries


def get_sector_categories():
    """Create multiselect list for sectors.

    Todo:
        Refactor when Codelists updated for optimal awesome.

    """
    sector_list = list()

    sector_codelist = iati.default.codelist('Sector')
    sector_category_codelist = iati.default.codelist('SectorCategory')

    all_the_codes = list(sector_category_codelist.codes) + list(sector_codelist.codes)

    for sector_code in all_the_codes:
        sector = dict()
        sector['code'] = sector_code.value
        sector['name'] = sector_code.name
        sector_list.append(sector)

    sector_list = sorted(sector_list, key=lambda sector: str(sector['code']))
    return sector_list
