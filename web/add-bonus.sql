CREATE TABLE "card" (
    "id" INTEGER PRIMARY KEY AUTOINCREMENT,
    "number" TEXT,
    "percent" INTEGER DEFAULT (10)
);


CREATE TABLE "bonus"(
  "id" INTEGER PRIMARY KEY AUTOINCREMENT,
  "date" INTEGER,
  "price" INTEGER(0),
  "haircut" INTEGER(0)
);

CREATE TABLE "discount"(
  "id" INTEGER PRIMARY KEY AUTOINCREMENT,
  "date" INTEGER,
  "price" INTEGER(0),
  "haircut" INTEGER(0)
);


CREATE INDEX "bonus_haircut_idx" on bonus (haircut ASC);
CREATE INDEX "discount_haircut_idx" on discount (haircut ASC);


ALTER table "haircut"  ADD "card" INTEGER;
CREATE INDEX "haircut_card_idx" on haircut (card ASC);

