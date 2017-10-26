"""A module to test form helper functions."""
from flask.models import form_helper


TEST_CSV_PATH = 'tests/test_data/test.csv'
TEST_CACHE_FILE = 'tests/test_data/mock_cache.json'


class TestFormHelper(object):
    """A class containing tests for form helper functions.

    Todo:
        Refactor till DRY AF.

    """

    def test_csv_to_array_returns_populated_list(self):
        """Check a list is populated."""
        result = form_helper.csv_to_array(TEST_CSV_PATH)
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
        # cache_file = 'tests/test_data/mock_cache.json'
        result = form_helper.reporting_orgs(TEST_CACHE_FILE)
        assert isinstance(result, dict)
        assert result == {"1":"this is an id1","2":"this is an id2","3":"this is an id3"}

    def test_get_countries_returns_list(self):
        """Check function creates list with expected formatted values."""
        result = form_helper.get_countries()
        assert isinstance(result, list)
        assert 'Kazakhstan' in result

    # def test_get_sector_categories_returns_populated_dict(self):
    #     """Check function creates a populated dict as expected."""
    #     result = form_helper.get_sector_categories(sector_category_file='tests/test_data/sector_category_test.csv')
    #     assert result == [{
    #         'category': '111',
    #         'category_name': 'Education, level unspecified',
    #         'data': [
    #             ['11110', 'Education policy and administrative management', 'Education sector policy, planning and programmes; aid to education ministries, administration and management systems; institution capacity building and advice; school management and governance; curriculum and materials development; unspecified education activities.', 'en', '111', 'Education, level unspecified', 'Education sector policy, planning and programmes; aid to education ministries, administration and management systems; institution capacity building and advice; school management and governance; curriculum and materials development; unspecified education activities.'],
    #             ['11120', 'Education facilities and training', 'Educational buildings, equipment, materials; subsidiary services to education (boarding facilities, staff housing); language training; colloquia, seminars, lectures, etc.', 'en', '111', 'Education, level unspecified', 'Education sector policy, planning and programmes; aid to education ministries, administration and management systems; institution capacity building and advice; school management and governance; curriculum and materials development; unspecified education activities.']
    #         ]
    #     }]
