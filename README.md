# Write apache stats to a log file in JSON format

```
├── httpd-helpers  <-- php script to dump apache stats to a log file
└── systemd        <-- systemd service+timer to run the php script every minute
```

- Copy `httpd-helpers` to `/etc/`
- Copy `systemd/apache_status_to_json.*` to `/etc/systemd/system`

Enable the service and timer:

```
systemctl enable apache_status_to_json.service
systemctl enable apache_status_to_json.timer
systemctl start apache_status_to_json.service
systemctl start apache_status_to_json.timer
```

Then check the log file: `/var/log/httpd/apache-status.log`
