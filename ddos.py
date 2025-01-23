import requests  
import hashlib  
import json  
import threading  

# API Endpoint  
api_url = 'https://penilaian.casperproject.my.id/api/v1/proposal'  

# API credentials  
api_id = 'FRZrzIac'  # Your API ID  
api_key = 'Vyvnc5fl8CQNFt59KRZmiSufksz9eXoy3zd6Q1cprO3I1yCLtGAeRCJk8RTdYzun'  # Your API Key  

# Create Signature  
signature = hashlib.md5(f"{api_id}:{api_key}".encode()).hexdigest()  

# NIM to filter  
nim = '2215354079'  # Change this to the desired NIM  

# Data to send in the body  
request_data = {  
    'nim': nim  
}  

def send_request():  
    headers = {  
        'Content-Type': 'application/json',  
        'key': api_key,  
        'signature': signature,  
    }  
    
    try:  
        response = requests.post(api_url, headers=headers, data=json.dumps(request_data))  
        
        # Display only the status code  
        print(f"Response Code: {response.status_code}")  
    except requests.exceptions.RequestException as e:  
        print(f"Request failed: {e}")  

# Number of requests to send  
num_requests = 50  # Adjust this number based on your testing needs  

threads = []  

# Create and start threads for sending requests  
for i in range(num_requests):  
    thread = threading.Thread(target=send_request)  
    threads.append(thread)  
    thread.start()  

# Wait for all threads to complete  
for thread in threads:  
    thread.join()  

print("Requests completed.")