# moneypit-ping

- Clone repo `git clone https://github.com/moneypit/moneypit-ping`
- Install PHP dependencies `php composer.phar install`
- Rename `config_sample.json` to `config.json`
- Update config with hosts to monitor and ES instance

- Setup cron job to ping and post stats to ES index defined in config

```

* * * * * php /home/pi/moneypit-ping/ping-hosts.php

```
