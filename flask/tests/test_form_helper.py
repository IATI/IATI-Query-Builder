"""Docstring."""
from flask.models import form_helper


TEST_CSV_PATH = 'tests/test_data/test.csv'
TEST_CACHE_FILE = ''


class TestFormHelper(object):
    """Docstring."""

    def test_csv_to_array_returns_populated_list(self):
        """Docstring."""
        result = form_helper.csv_to_array(TEST_CSV_PATH)
        assert isinstance(result, list)
        assert result != list()

    def test_build_sanitised_multi_select_values_returns_populated_list(self):
        """Docstring."""
        sanitized_values = ['cat', 'penguin']
        allowed_values_path = 'tests/test_data/allowed_values_test.csv'
        result = form_helper.build_sanitised_multi_select_values(allowed_values_path, sanitized_values)
        assert isinstance(result, list)
        assert len(result) == 2

    def test_reporting_orgs_returns_sorted_dictionary(self):
        """Docstring."""
        cache_file = 'tests/test_data/mock_cache.json'
        result = form_helper.reporting_orgs(cache_file)
        assert isinstance(result, dict)
        assert result == {"1":"this is an id1","2":"this is an id2","3":"this is an id3"}
