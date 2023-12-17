# ViberBot with PaLM2 API Integration

## Overview
Viberbot integrated with the PaLM2 API from Google AI. The bot is built using PHP Laravel and used Ngrok for secure tunneling during development.


## Features

- **Viber Integration:** Seamlessly connect with Viber users, allowing them to interact with the bot directly on the Viber platform.

- **PaLM2 API Integration:** PaLM2 is a factual language model from Google AI, trained on a massive dataset of text and code. It is the successor to PaLM, and it is one of the most powerful language models in the world. PaLM2 can generate text, translate languages, write different kinds of creative content, and answer your questions in an informative way. It is still under development, but it has learned to perform many kinds of tasks..


- **Ngrok & Laravel for Development:** Utilize Ngrok to create a secure tunnel to your local development environment, facilitating testing and debugging and Laravel for backend development.

## Getting Started

### Prerequisites
- Install Laravel
- Set up Viber API credentials
- Obtain PaLM2 API key
- Install Ngrok for local development

### Installation
1. Clone the repository: `git clone https://github.com/your-username/your-repo.git`
2. Install dependencies: `composer install`
3. Configure Viber API credentials, PaLM2 API key, and other necessary settings.
4. Run the development server: `php artisan serve`
5. Run your ngrok development server: `ngrok http --domain=<YOUR-NGROK-URL> 8000`

## .env
```
NGROK_ENDPOINT=<Your-ngrok-endpoint>

VIBER_KEY=<your-viber-apikey>

PALM_API_KEY=<your-palm2-key>
PALM_API_CHAT_ENDPOINT=<chat-endpoint-url>
PALM_API_TEXT_ENDPOINT=<text-endpoint-url>
```

## Usage
1. Register your bot on Viber platform and configure the necessary settings. [Viber Partners](https://partners.viber.com/login)
2. Interact with the bot on the Viber platform.

## Contributing
Contributions are welcome!
Feel free to explore, contribute, and enhance the capabilities of this ViberBot integrated with PaLM2 API. Happy coding!




