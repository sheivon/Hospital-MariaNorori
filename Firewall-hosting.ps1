#Forward WSL port to Windows LAN IP
netsh interface portproxy add v4tov4 listenaddress=192.168.1.204 listenport=3307 connectaddress=172.24.22.226 connectport=3307
netsh interface portproxy add v4tov4 listenaddress=0.0.0.0 listenport=3307 connectaddress=172.24.22.226 connectport=3307

#Allow through Windows Firewall
New-NetFirewallRule -DisplayName "Share WSL MySQL" -Direction Inbound -Protocol TCP -LocalPort 3307 -Action Allow
New-NetFirewallRule -DisplayName "Share WSL MySQL" -Direction Outbound -Protocol TCP -LocalPort 3307 -Action Allow

#local hosting
New-NetFirewallRule -DisplayName "Share WSL MySQL" -Direction Inbound -Protocol TCP -LocalPort 8000 -Action Allow
New-NetFirewallRule -DisplayName "Share WSL MySQL" -Direction Outbound -Protocol TCP -LocalPort 8000 -Action Allow