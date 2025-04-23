import csv
import os
from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
from webdriver_manager.chrome import ChromeDriverManager
from selenium.webdriver.chrome.service import Service

# File path for storing data
csv_file_path = 'C:\\Users\\lenovo\\Desktop\\DRS\\ai\\training_data\\scraping\\observation data\\streamflow_obv.csv'

# Set up ChromeDriver with the appropriate service
service = Service(ChromeDriverManager().install())
driver = webdriver.Chrome(service=service)

# Open the URL
url = "https://www.dhm.gov.np/hydrology/realtime-stream"
driver.get(url)

# Wait for the tab to become clickable and switch to it
# tab = WebDriverWait(driver, 20).until(
#     EC.element_to_be_clickable((By.XPATH, "//*[@id='myTab']/li[6]/a"))
# )
# tab.click()

# Wait for the table rows to load
WebDriverWait(driver, 20).until(
    EC.presence_of_all_elements_located((By.XPATH, '//*[@id="tablegeneral"]/tbody/tr'))
)

# Scrape the table rows
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

# Determine if the file already exists
file_exists = os.path.isfile(csv_file_path)

# Write or append the data to CSV
if data_to_save:
    with open(csv_file_path, mode='a', newline='', encoding='utf-8') as file:
        writer = csv.writer(file)

        # Write header only if the file does NOT exist
        if not file_exists:
            writer.writerow(["S.No.", "Basin Name", "Station Index", "Station Name", "District Name", "Water Level(m)", "Discharge(m3/s)"])

        # Append the data rows
        writer.writerows(data_to_save)

# Close the driver after scraping
driver.quit()
