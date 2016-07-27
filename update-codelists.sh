# CODELIST SOURCES:
# http://iatistandard.org/codelists/downloads/clv1/codelist/Country.csv
# http://iatistandard.org/codelists/downloads/clv1/codelist/Region.csv
# http://iatistandard.org/codelists/downloads/clv1/codelist/Sector.csv
# http://iatistandard.org/codelists/downloads/clv1/codelist/SectorCategory.csv
# http://iatistandard.org/codelists/downloads/clv1/codelist/OrganisationType.csv

# Remove contents of the codelists folder
rm codelists/*
cd codelists

# Perform the loop to get each codelist
for codelist_name in "Country" "Region" "Sector" "SectorCategory" "OrganisationType"; do
    wget "http://iatistandard.org/codelists/downloads/clv1/codelist/$codelist_name.csv"
done

# Go back to the root directory
cd ../