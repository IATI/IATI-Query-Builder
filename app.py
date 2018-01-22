"""Controller module for Query Builder app."""
from flask import Flask, render_template
from .models import form_helper

app = Flask(__name__)


@app.route('/')
def index():
    reporting_orgs = form_helper.sorted_reporting_orgs()
    countries = form_helper.get_codelist_values('Country', lambda x: x.lower())
    regions = form_helper.get_codelist_values('Region', lambda x: int(x))
    org_types = form_helper.get_codelist_values('OrganisationType', lambda x: x.lower())
    sector_categories = form_helper.get_sector_categories()
    non_escaped_html = '<br/>'

    return render_template('index.html', reporting_orgs=reporting_orgs, countries=countries, regions=regions, org_types=org_types, sector_categories=sector_categories, non_escaped_html=non_escaped_html)


if __name__ == '__main__':
    app.debug = True
    app.run()
