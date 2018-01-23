"""Controller module for Query Builder app."""
from flask import Flask, render_template
from .models import multiselect_menus

app = Flask(__name__)


@app.route('/')
def index():
    """Render main template with core variables assigned prospective values."""
    reporting_orgs = multiselect_menus.sorted_reporting_orgs()
    countries = multiselect_menus.get_codelist_values('Country', lambda x: x.lower())
    regions = multiselect_menus.get_codelist_values('Region', int)
    org_types = multiselect_menus.get_codelist_values('OrganisationType', lambda x: x.lower())
    sector_categories = multiselect_menus.get_sector_categories()
    non_escaped_html = '<br/>'

    return render_template('index.html', reporting_orgs=reporting_orgs, countries=countries, regions=regions, org_types=org_types, sector_categories=sector_categories, non_escaped_html=non_escaped_html)


if __name__ == '__main__':
    app.debug = True
    app.run()
