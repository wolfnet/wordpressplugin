# -*- mode: ruby -*-
# vi: set ft=ruby :

Vagrant.configure("2") do |config|

    config.vm.box = "ubuntu-precise64-lamp"
    config.vm.box_url = "//wolfnet11/Public/Dev/ubuntu-precise64-lamp.box"

    config.vm.define :latest do |latest|

        latest.vm.network :private_network, ip: "192.168.50.1"

        # Download and extract WordPress files.
        latest.vm.provision :shell, :inline => "wget -qO wordpress.tar.gz http://wordpress.org/latest.tar.gz"
        latest.vm.provision :shell, :inline => "tar -xzf wordpress.tar.gz"
        latest.vm.provision :shell, :inline => "rm -rf wordpress.tar.gz /var/www"
        latest.vm.provision :shell, :inline => "mv wordpress /var/www"
        latest.vm.provision :shell, :inline => "ln -s /vagrant/.vagrant/wp-config.php /var/www/wp-config.php"

        latest.vm.provision :shell, :inline => "cp -f /vagrant/.vagrant/wp-htaccess /var/www/.htaccess"

        # Create a symbolic clink representing the project data.
        latest.vm.provision :shell, :inline => "ln -fs /vagrant /var/www/wp-content/plugins/vagrant"

        # Run some database queries to prep the server for WordPress install.
        latest.vm.provision :shell, :inline => "cat /vagrant/.vagrant/wp-prep.sql | mysql -u root"

    end

    config.vm.define :nightly do |nightly|

        nightly.vm.network :private_network, ip: "192.168.50.2"

        # Download and extract WordPress files.
        nightly.vm.provision :shell, :inline => "wget -qO wordpress.zip http://wordpress.org/nightly-builds/wordpress-latest.zip"
        nightly.vm.provision :shell, :inline => "unzip -qq wordpress.zip"
        nightly.vm.provision :shell, :inline => "rm -rf wordpress.zip /var/www"
        nightly.vm.provision :shell, :inline => "mv wordpress /var/www"
        nightly.vm.provision :shell, :inline => "ln -s /vagrant/.vagrant/wp-config.php /var/www/wp-config.php"

        nightly.vm.provision :shell, :inline => "cp -f /vagrant/.vagrant/wp-htaccess /var/www/.htaccess"

        # Create a symbolic clink representing the project data.
        nightly.vm.provision :shell, :inline => "ln -fs /vagrant /var/www/wp-content/plugins/vagrant"

        # Run some database queries to prep the server for WordPress install.
        nightly.vm.provision :shell, :inline => "cat /vagrant/.vagrant/wp-prep.sql | mysql -u root"

    end

    config.vm.define :wp3_5_1 do |wp3_5_1|

        wp3_5_1.vm.network :private_network, ip: "192.168.50.3"

        # Download and extract WordPress files.
        wp3_5_1.vm.provision :shell, :inline => "wget -qO wordpress.tar.gz http://wordpress.org/wordpress-3.5.1.tar.gz"
        wp3_5_1.vm.provision :shell, :inline => "tar -xzf wordpress.tar.gz"
        wp3_5_1.vm.provision :shell, :inline => "rm -rf wordpress.tar.gz /var/www"
        wp3_5_1.vm.provision :shell, :inline => "mv wordpress /var/www"
        wp3_5_1.vm.provision :shell, :inline => "ln -s /vagrant/.vagrant/wp-config.php /var/www/wp-config.php"

        wp3_5_1.vm.provision :shell, :inline => "cp -f /vagrant/.vagrant/wp-htaccess /var/www/.htaccess"

        # Create a symbolic clink representing the project data.
        wp3_5_1.vm.provision :shell, :inline => "ln -fs /vagrant /var/www/wp-content/plugins/vagrant"

        # Run some database queries to prep the server for WordPress install.
        wp3_5_1.vm.provision :shell, :inline => "cat /vagrant/.vagrant/wp-prep.sql | mysql -u root"
        wp3_5_1.vm.provision :shell, :inline => "cat /vagrant/.vagrant/wp3.5.1-setup.sql | mysql -u root"

    end

    config.vm.define :wp3_3 do |wp3_3|

        wp3_3.vm.network :private_network, ip: "192.168.50.4"

        # Download and extract WordPress files.
        wp3_3.vm.provision :shell, :inline => "wget -qO wordpress.tar.gz http://wordpress.org/wordpress-3.3.tar.gz"
        wp3_3.vm.provision :shell, :inline => "tar -xzf wordpress.tar.gz"
        wp3_3.vm.provision :shell, :inline => "rm -rf wordpress.tar.gz /var/www"
        wp3_3.vm.provision :shell, :inline => "mv wordpress /var/www"
        wp3_3.vm.provision :shell, :inline => "ln -s /vagrant/.vagrant/wp-config.php /var/www/wp-config.php"

        wp3_3.vm.provision :shell, :inline => "cp -f /vagrant/.vagrant/wp-htaccess /var/www/.htaccess"

        # Create a symbolic clink representing the project data.
        wp3_3.vm.provision :shell, :inline => "ln -fs /vagrant /var/www/wp-content/plugins/vagrant"

        # Run some database queries to prep the server for WordPress install.
        wp3_3.vm.provision :shell, :inline => "cat /vagrant/.vagrant/wp-prep.sql | mysql -u root"
        wp3_3.vm.provision :shell, :inline => "cat /vagrant/.vagrant/wp3.3-setup.sql | mysql -u root"

    end

    config.vm.define :wpmu3_5_1 do |wpmu3_5_1|

        wpmu3_5_1.vm.network :private_network, ip: "192.168.50.101"

        # Download and extract WordPress files.
        wpmu3_5_1.vm.provision :shell, :inline => "wget -qO wordpress.tar.gz http://wordpress.org/wordpress-3.5.1.tar.gz"
        wpmu3_5_1.vm.provision :shell, :inline => "tar -xzf wordpress.tar.gz"
        wpmu3_5_1.vm.provision :shell, :inline => "rm -rf wordpress.tar.gz /var/www"
        wpmu3_5_1.vm.provision :shell, :inline => "mv wordpress /var/www"
        wpmu3_5_1.vm.provision :shell, :inline => "ln -s /vagrant/.vagrant/wpmu-config.php /var/www/wp-config.php"

        wpmu3_5_1.vm.provision :shell, :inline => "cp -f /vagrant/.vagrant/wpmu-htaccess /var/www/.htaccess"

        # Create a symbolic clink representing the project data.
        wpmu3_5_1.vm.provision :shell, :inline => "ln -fs /vagrant /var/www/wp-content/plugins/vagrant"

        # Run some database queries to prep the server for WordPress install.
        wpmu3_5_1.vm.provision :shell, :inline => "cat /vagrant/.vagrant/wp-prep.sql | mysql -u root"
        #wpmu3_5_1.vm.provision :shell, :inline => "cat /vagrant/.vagrant/wpmu3.5.1-setup.sql | mysql -u root"

    end

end
