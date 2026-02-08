# 1) เอาโฟลเดอร์ออกจาก git (ครั้งเดียว)
git rm -r --cached mysql-data

# 2) ignore
echo "mysql-data/" >> .gitignore
git commit -m "ignore mysql-data"

# 3) ต่อไป pull ได้สบาย
git pull


git fetch origin
git checkout origin/main -- app/
