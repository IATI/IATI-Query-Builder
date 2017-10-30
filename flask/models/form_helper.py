"""Helper functions for form."""
import csv
import json
import iati


CACHEFILE = 'groups_cache_dc.json'


def csv_to_array(path):
    """Add non-title rows of CSV file to list.

    Note:
        This replaces WET functions 'get_regions', and 'get_org_types' in original code.

    Todo:
        Test for exceptions.

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
        pass

def html_escape_filter(value):
    """Convert special characters to HTML character values."""
    return value.replace('&', '&amp;').replace('"', '&quot;').replace('<', '&lt;').replace('>', '&gt;')


def build_sanitised_multi_select_values(path, sanitized_values):
    """Check values of a list are permitted and add to multi-select list.

    Todo:
        Rename this function to describe actual purpose. Maybe something like `filter_permitted_values`.

    """
    values = list()
    allowed_values = csv_to_array(path)

    if (sanitized_values != list()):
        for requested_value in sanitized_values:
            if (requested_value in allowed_values) and (requested_value is not None):
                values.append(requested_value)

    if (values == list()):
        return None
    return values


def reporting_orgs(cache_file=CACHEFILE):
    """Return sorted dictionary for organisations."""
    reporting_orgs = dict()
    excluded_ids = ['To be confirmed.']

    with open(cache_file) as c_file:
        groups = json.load(c_file)

        for key, value in groups.items():
            if value['packages'] is not None:
                publisher_iati_id = value['extras']['publisher_iati_id']
                if publisher_iati_id is not None:
                    if publisher_iati_id not in excluded_ids:
                        reporting_orgs[value['display_name']] = publisher_iati_id

    sorted_dict = dict()

    for key in sorted(reporting_orgs.keys()):
        sorted_dict[key] = reporting_orgs[key]

    return sorted_dict


def get_countries():
    """Format country list for multiselect."""
    countries = list()
    country_codelist = iati.default.codelist('Country')

    for country_code in country_codelist.codes:
        country_name = country_code.name

        html_escaped_name = html_escape_filter(country_name)

        convert_case_value = html_escaped_name.lower().title()
        countries.append(convert_case_value)

    return countries


def get_sector_categories():
    """Create multiselect list for sectors.

    Todo:
        Refactor when Codelists updated for optimal awesome.

    """
    sector_set = list()

    sector_codelist = iati.default.codelist('Sector')
    sector_category_codelist = iati.default.codelist('SectorCategory')

    all_the_codes = list(sector_category_codelist.codes) + list(sector_codelist.codes)

    for sector_code in all_the_codes:
        sector = dict()
        sector['code'] = sector_code.value
        sector['name'] = sector_code.name
        sector_set.append(sector)

    sector_set = sorted(sector_set, key=lambda sector: str(sector['code']))

    return sector_set
