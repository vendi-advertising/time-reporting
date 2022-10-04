# Time Reporting

## Installation

### Harvest developer setup

The Harvest _OAuth2 application_ is used to allow users to sign in to Time Reporting, and the _personal access token_
is used for global synchronization of objects.

#### OAuth2 application

1. Visit https://id.getharvest.com/developers
2. Click the _Create new OAuth2 application_ button
3. For the _Redirect URL_ enter `https://tr.wip/`
    * Replace as necessary
    * TODO: Bind to true redirect URL
4. Note the _Client ID_ and _Client secret_ for later use

#### Personal access token

1. Visit https://id.getharvest.com/developers
2. Click the _Create new personal access token_ button
3. Enter a name and press the _Create personal access token_ button
4. If you have more than one account, change to the one you want to use
5. Note your _Token_ and _Account ID_ for later use

### Symfony setup

#### Generate a new secret

1. Run `symfony console app:generate-secret`
2. Note the `APP_SECRET`

#### Update environment variables

1. Copy `.env` to `.env.local`
2. Set the environment variables that start with `HARVEST_` accordingly
3. Update the `DATABASE_URL` variable accordingly
4. Update `APP_SECRET`
5. Run `symfony console doctrine:migrations:migrate`

### Application setup

Run the following commands:

```shell
symfony console app:harvest:import:clients
symfony console app:harvest:import:users
symfony console app:harvest:import:users
symfony console app:harvest:import:tasks
symfony console app:harvest:import:projects
symfony console app:harvest:import:project-budgets
```

## Usage

Visit https://tr.wip/login