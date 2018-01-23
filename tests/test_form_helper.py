"""A module to test form helper functions.

Todo:
    Add acceptance test framework and test. Maybe Capybara and Selenium?

"""
from models import multiselect_menus


TEST_CSV_PATH = 'tests/test_data/test.csv'
TEST_CACHE_FILE = 'tests/test_data/mock_cache.json'


class TestFormHelper(object):
    """A class containing tests for form helper functions.

    Todo:
        Refactor till DRY AF.

    """

    def test_reporting_orgs_returns_sorted_dictionary(self):
        """Check function creates a JSON compatible sorted dictionary."""
        result = multiselect_menus.sorted_reporting_orgs(TEST_CACHE_FILE)
        assert isinstance(result, dict)
        assert result == {"1":"this is an id1","2":"this is an id2","3":"this is an id3"}

    def test_get_sector_categories_returns_populated_dict(self):
        """Check function creates a populated dict as expected."""
        result = multiselect_menus.get_sector_categories()
        assert isinstance(result, list)
        assert result[0]['code'] == '111'
        assert result[-1]['code'] == '99820'
