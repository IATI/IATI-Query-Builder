"""A module to test form helper functions.

Todo:
    Add acceptance test framework and test. Maybe Capybara and Selenium?

"""
from models import form_helper


TEST_CSV_PATH = 'tests/test_data/test.csv'
TEST_CACHE_FILE = 'tests/test_data/mock_cache.json'


class TestFormHelper(object):
    """A class containing tests for form helper functions.

    Todo:
        Refactor till DRY AF.

    """

    def test_csv_to_list_returns_populated_list(self):
        """Check a list is populated."""
        result = form_helper.csv_to_list(TEST_CSV_PATH)
        assert isinstance(result, list)
        assert result != list()

    def test_build_sanitised_multi_select_values_returns_populated_list(self):
        """Check a list is populated as expected with permitted values."""
        sanitized_values = ['cat', 'penguin']
        allowed_values_path = 'tests/test_data/allowed_values_test.csv'
        result = form_helper.build_sanitised_multi_select_values(allowed_values_path, sanitized_values)
        assert isinstance(result, list)
        assert len(result) == 2
        assert 'duck' not in result

    def test_reporting_orgs_returns_sorted_dictionary(self):
        """Check function creates a JSON compatible sorted dictionary."""
        result = form_helper.reporting_orgs(TEST_CACHE_FILE)
        assert isinstance(result, dict)
        assert result == {"1":"this is an id1","2":"this is an id2","3":"this is an id3"}

    def test_get_countries_returns_dict(self):
        """Check function creates list with expected formatted values."""
        result = form_helper.get_countries()
        assert isinstance(result, dict)
        assert 'Kazakhstan' in result

    def test_get_sector_categories_returns_populated_dict(self):
        """Check function creates a populated dict as expected."""
        result = form_helper.get_sector_categories()
        assert isinstance(result, list)
        assert result[0]['code'] == '111'
        assert result[-1]['code'] == '99820'
