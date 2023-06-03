import os
import requests

folder_path = "engine"  # Update this path with your specific directory
api_endpoint = "https://api.openai.com/v1/engines/davinci-codex/completions"  # Correct API endpoint
api_key = os.getenv("CHATGPT_API_KEY")  # Read API Key from the environment variable

# Concatenate all PHP files in the given directory
def concatenate_files():
    files_content = ""
    
    for file in os.listdir(folder_path):
        if file.endswith(".php"):
            with open(os.path.join(folder_path, file), 'r') as f:
                files_content += f.read() + '\n'
                
    return files_content

# Send a request to the ChatGPT API
def ask_chatgpt(question):
    headers = {
        "Content-Type": "application/json",
        "Authorization": f"Bearer {api_key}"
    }
    data = {
        "prompt": question,
        "max_tokens": 150
    }
    response = requests.post(api_endpoint, headers=headers, json=data)
    if response.status_code == 200:
        return response.json()["choices"][0]["text"]
    else:
        raise Exception(f"Request to ChatGPT API failed with status code: {response.status_code}")

def main():
    question = concatenate_files()
    answer = ask_chatgpt("Create an outline of all classes and their methods with a brief description of their purpose. Keep it minial and use the folling php code. " + question)
    
    file = open("documentation.txt", 'w')
    file.write(answer)
    file.close()

if __name__ == "__main__":
    main()
