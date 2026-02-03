import openpyxl
import os

file_path = 'test m.xlsx'

if not os.path.exists(file_path):
    print(f"File not found: {repr(file_path)}")
    exit(1)

try:
    wb = openpyxl.load_workbook(file_path, data_only=True)
    sheet = wb.active
    with open('excel_output.txt', 'w', encoding='utf-8') as f:
        f.write(f"Sheet Name: {sheet.title}\n")
        f.write("\n--- Content ---\n")
        for row in sheet.iter_rows(values_only=True):
            if any(cell is not None for cell in row):
                f.write(str(list(row)) + "\n")
            
except Exception as e:
    with open('excel_output.txt', 'w', encoding='utf-8') as f:
        f.write(f"Error reading excel: {e}")
