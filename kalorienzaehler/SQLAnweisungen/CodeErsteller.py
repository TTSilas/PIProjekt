import requests

def fetch_products(page=1):
    url = "https://world.openfoodfacts.org/api/v2/search"
    params = {
        "page": page,
        "page_size": 50,
        "fields": "product_name,code,nutriments,categories",
        "lc": "de",  # nur deutschsprachige Produkte bevorzugen
    }
    resp = requests.get(url, params=params, headers={"User-Agent": "MakroCollector/1.0"})
    return resp.json().get("products", [])

def main():
    seen = set()
    sql_makros, sql_prod = [], []
    makro_id, page = 1, 100

    while len(sql_makros) < 250:
        print(f"Hole Seite {page} ...")
        prods = fetch_products(page)
        if not prods:
            print("Keine Produkte mehr gefunden.")
            break

        for p in prods:
            code = p.get("code")
            n = p.get("nutriments", {})
            name = p.get("product_name", "").strip()
            categories = p.get("categories", "").strip()

            # Filter: Für französische Produkte
            if not name or any(fr in name.lower() for fr in ["au ", "de ", "le ", "la ", "avec ", "fromage", "pain"]):
                continue

            # Makronährstoffe vorhanden?
            if code and all(n.get(f"{x}_100g") is not None for x in ["carbohydrates", "fat", "proteins"]):
                if code in seen:
                    continue
                seen.add(code)

                k = n["carbohydrates_100g"]
                f = n["fat_100g"]
                e = n["proteins_100g"]
                safe_name = name.replace("'", "''")
                safe_cat = (categories.split(",")[0] or "Unbekannt").replace("'", "''")

                sql_makros.append(
                    f"INSERT INTO Makros (Kohlenhydrate, Fett, Eiweiss, MakroID) VALUES ({k:.2f}, {f:.2f}, {e:.2f}, {makro_id});"
                )
                sql_prod.append(
                    f"INSERT INTO Produkt (EAN, MakroID, Name, Kategorie) VALUES ({code}, {makro_id}, '{safe_name}', '{safe_cat}');"
                )

                makro_id += 1
                if len(sql_makros) >= 250:
                    break
        page += 1

    with open("makros_125.sql", "w", encoding="utf-8") as f:
        f.write("\n".join(sql_makros + [""] + sql_prod))

    print("✅ Fertig! makros_125.sql wurde erstellt.")

# Fehleranzeige, falls was schiefgeht
if __name__ == "__main__":
    try:
        main()
    except Exception as e:
        print("❌ FEHLER:", e)
