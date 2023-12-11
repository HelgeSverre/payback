# Payback

Fork of [https://github.com/jxxe/payback](https://github.com/jxxe/payback) that swaps out the OpenAI call with the [HelgeSverre/extractor](https://github.com/HelgeSverre/extractor) package, to showcase a real world example of usage.

All credits to original creator [Jerome Paulos](https://twitter.com/jeromepaulos).

[Original tweet](https://twitter.com/jeromepaulos/status/1732494690501419492)

## Setup

```
git clone git@github.com:HelgeSverre/payback.git
cd payback
composer install
npm i
npm run build

php artisan key:generate
php artisan migrate:fresh --seed
```
