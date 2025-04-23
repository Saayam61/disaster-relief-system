import csv
from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
from webdriver_manager.chrome import ChromeDriverManager
from selenium.webdriver.chrome.service import Service

# Set up ChromeDriver with the appropriate service
service = Service(ChromeDriverManager().install())
driver = webdriver.Chrome(service=service)

# Open the URL
url = "https://www.dhm.gov.np/hydrology/river-watch"
driver.get(url)

# Wait for the tab to become clickable and switch to it
tab = WebDriverWait(driver, 20).until(
    EC.element_to_be_clickable((By.XPATH, "//*[@id='myMapTblTab']/li[2]/a"))
)
tab.click()

# Wait for the table rows to load
WebDriverWait(driver, 20).until(
    EC.presence_of_all_elements_located((By.XPATH, '//*[@id="tablegeneral"]/tbody/tr'))
)

# Now scrape the table rows
rows = driver.find_elements(By.XPATH, '//*[@id="tablegeneral"]/tbody/tr')

# Prepare the data for writing to a CSV
data_to_save = []
if rows:
    for row in rows:
        cols = row.find_elements(By.TAG_NAME, "td")
        data = [col.text for col in cols]
        data_to_save.append(data)
else:
    print("No rows found.")

# Write the data to a CSV file (rewriting the file each time)
if data_to_save:
    # Specify the file path where you want to save the CSV
    with open(f'C:\\Users\\lenovo\\Desktop\\DRS\\ai\\training_data\\scraping\\nrt data\\riverwatch_nrt.csv', mode='w', newline='', encoding='utf-8') as file:
        writer = csv.writer(file)
        # Write the header row
        writer.writerow(["S.No.", "Basin Name", "Station Name", "District Name", "Water Level(m)", "Warning Level(m)", "Danger Level(m)"])
        # Write the data rows
        writer.writerows(data_to_save)

# Close the driver after scraping
driver.quit()
