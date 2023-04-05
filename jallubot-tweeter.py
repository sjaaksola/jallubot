import requests
import tweepy
from bs4 import BeautifulSoup
import random
import time

# Ladataan Alkon sivu tietylle tuotteelle
url = "https://www.alko.fi/INTERSHOP/web/WFS/Alko-OnlineShop-Site/fi_FI/-/EUR/ViewProduct-Include?SKU=000706"
page = requests.get(url)

# Parsitaan sivun HTML BeautifulSoup kirjaston avulla
soup = BeautifulSoup(page.content, "html.parser")

# Etsitään kaikki myymälät, jotka myyvät tuotetta ja niiden saatavuustiedot
store_items = soup.find_all(class_="store-item")

# Tallennetaan saatavuustiedot sanakirjaan
stock_info = {}
for store in store_items:
    store_name = store.find(class_="store-in-stock").get_text()
    stock = store.find(class_="number-in-stock").get_text()
    stock_info[store_name] = stock

# Valitaan satunnainen myymälä ja saatavuustieto
store_name = random.choice(list(stock_info.keys()))
stock = stock_info[store_name]

# Lähetetään tweetti, joka sisältää satunnaisen saatavuustiedon
consumer_key = "YnPkVu6jestqFxbD9p7eTEGje"
consumer_secret = "vdC4Idv9d8MQZBdF4govY60GepHlNR9ciwrrykE9kPBVnYpje5"
access_token = "1643559237828636672-ZK788yvYdZXRcF8ZNfVgFz3Au3fFpZ"
access_token_secret = "NUTM9YY8XfHpwXls2LhfHX3oiyDkdbkKR6adijkAMYg8N"

auth = tweepy.OAuthHandler(consumer_key, consumer_secret)
auth.set_access_token(access_token, access_token_secret)

api = tweepy.API(auth)

# Tweetin teksti
tweet_text = "Yhden tähden jallua löytyy " + store_name + ": " + stock

# Satunnaisen viiveen lisääminen
sleep_time = random.randint(30, 60) * 60 # Sekunneissa
time.sleep(sleep_time)

# Lähetetään tweetti
api.update_status(tweet_text)
