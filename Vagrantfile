# -*- mode: ruby -*-
# vi: set ft=ruby :

Vagrant.configure(2) do |config|
  config.vm.box = "ubuntu/trusty64"
  config.vm.network "forwarded_port", guest: 80, host: 8080
  config.vm.provision "shell", path: "deploy.sh"

  config.vm.synced_folder "./", "/vagrant", id: "vagrant-root",
    owner: "vagrant",
    group: "www-data",
    mount_options: ["dmode=775,fmode=664"]

  config.vm.provider "virtualbox" do |v|
    v.customize ["modifyvm", :id, "--nictype1", "virtio"]
    v.cpus = 2
    v.memory = 512
  end

  #   # Customize the amount of memory on the VM:
  #   vb.memory = "1024"

  # Enable provisioning with a shell script. Additional provisioners such as
  # Puppet, Chef, Ansible, Salt, and Docker are also available. Please see the
  # documentation for more information about their specific syntax and use.
  # config.vm.provision "shell", inline: <<-SHELL
  #   sudo apt-get update
  #   sudo apt-get install -y apache2
  # SHELL
end
