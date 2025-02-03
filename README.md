# Receiptman

A command line application that automatically generates Docker environment.

## How to use

**I assume that the user environment already have a Docker installed locally**

1. Navigate to the folder root.
2. Run `docker compose up -d --build`
3. Make the root file `receipt` executable: type in terminal `chmod +x receipt`
4. Run the receipt script: type in terminal `./receipt`

The las command will give you hints how to use. It will present you everything that the script can currently do. For exemple, if you want to create a php environment, execute in terminal:
```
./receipt receipt:php-full-dev
```