import urllib.parse
from sqlalchemy import create_engine, text

with open('palVarnan/.env') as f:
    env = dict(line.strip().split('=', 1) for line in f if '=' in line and not line.startswith('#'))

user = env['DB_USERNAME']
pw = urllib.parse.quote_plus(env['DB_PASSWORD'])
host = env['DB_HOST']
port = env['DB_PORT']
db = env['DB_DATABASE']

db_url = f"mysql+pymysql://{user}:{pw}@{host}:{port}/{db}?ssl_verify_cert=true&ssl_verify_identity=true"
engine = create_engine(db_url)
with engine.connect() as conn:
    rows = conn.execute(text('SELECT id, product_id, varient, product_qty, offer_price, mrp_price FROM product_varients WHERE product_id = 34')).fetchall()
    print('Product 34 variants:')
    for r in rows:
        print(dict(r._mapping))
