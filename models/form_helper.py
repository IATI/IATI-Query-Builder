"""A module to generate refined data for form selection menus."""
import csv
import json
import iati


CACHEFILE = 'publisher_cache.json'


def sort_dict_by_keys(dictionary_to_sort, key_function):
    """Sort a dictionary by its keys."""
    sorted_dict = dict()
    for key in sorted(dictionary_to_sort.keys(), key=lambda x: key_function(x)):
        sorted_dict[key] = dictionary_to_sort[key]
    return sorted_dict


def sorted_reporting_orgs(cache_file=CACHEFILE):
    """Return sorted dictionary for reporting organisations."""
    reporting_orgs = dict()

    with open(cache_file) as c_file:
        org_info = json.load(c_file)

        for org in org_info:
            if org['package_count'] > 0:
                reporting_orgs[org['display_name']] = org['publisher_iati_id']

    sorted_orgs = sort_dict_by_keys(reporting_orgs, lambda x: x.lower())

    return sorted_orgs


def get_codelist_values(codelist_name, sort_func):
    """Format list for multiselect from a given codelist."""
    codelist_values = dict()
    codelist = iati.default.codelist(codelist_name)

    for code in codelist.codes:
        name = code.name
        codelist_values[code.value] = code.name

    sorted_codelist_values = sort_dict_by_keys(codelist_values, sort_func)

    return sorted_codelist_values


def get_sector_categories():
    """Create multiselect list for sectors.

    Todo:
        Keep an eye on pyIATI Codelist updates for future possible optimisations.

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
