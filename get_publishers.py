import requests

from ckanapi import RemoteCKAN
import json


CKAN_API_URL = 'https://iatiregistry.org'


def make_publisher_cache():
    """Create cache file of registry publisher info."""
    registry = RemoteCKAN(CKAN_API_URL)

    dict_of_publishers = registry.action.organization_list(all_fields=True, include_extras=True)

    with open('publisher_cache.json', 'w') as outfile:
        json.dump(dict_of_publishers, outfile)
