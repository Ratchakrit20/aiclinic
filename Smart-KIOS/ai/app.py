from flask import Flask, jsonify, request, send_file
import mysql.connector
from transformers import pipeline,VitsModel, AutoTokenizer
import torch
from flask_cors import CORS
from gtts import gTTS
import os
from huggingface_hub import login
import torchaudio

# Login to the Hugging Face Hub
login("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")
# Load model and tokenizer
model = VitsModel.from_pretrained("facebook/mms-tts-tha")
tokenizer = AutoTokenizer.from_pretrained("facebook/mms-tts-tha")


app = Flask(__name__)
CORS(app, origins=['http://localhost:8080'])
# กำหนดค่าการเชื่อมต่อฐานข้อมูล
db_config = {
    'host': 'db',
    'port':'3306',
    'user': 'root',
    'password': '1234',
    'database': 'mydb',
}






# ใส่โค้ดส่วนที่ใช้ Transformers ที่นี่
MODEL_NAME = "biodatlab/whisper-th-small-combined"  # specify the model name
lang = "th"  # change to Thai langauge

device = 0 if torch.cuda.is_available() else "cpu"

pipe = pipeline(
    task="automatic-speech-recognition",
    model=MODEL_NAME,
    chunk_length_s=30,
    device=device,
)
pipe.model.config.forced_decoder_ids = pipe.tokenizer.get_decoder_prompt_ids(
  language=lang,
  task="transcribe"
)



@app.route('/')
def home():
    return send_file("output1.wav")
# เรียกใช้ฟังก์ชันนี้เพื่อเรียกหน้าเว็บ


@app.route('/upload', methods=['POST'])
def upload_file():
    if 'audio' not in request.files:
        return jsonify({'error': 'No file sent.'}), 400

    audio_file = request.files['audio']
    user_id = request.form.get('userID')
    slide_index = request.form.get('slideIndex')
    print(audio_file)
    if audio_file.filename == '':
        return jsonify({'error': 'No selected file.'}), 400

    dest_path = os.path.join(audio_file.filename)

    try:
        audio_file.save(dest_path)
        result = pipe(dest_path)
        # result["text"]
        inputs = tokenizer(result["text"], return_tensors="pt")

        # Generate speech waveform
        with torch.no_grad():
            output = model(**inputs).waveform

        # Convert to numpy array
        audio_waveform = output.cpu().numpy()

        # Save the waveform as a WAV file
        torchaudio.save("output1.wav", torch.tensor(audio_waveform), sample_rate=16000)

        # Save the tokenizer
        tokenizer.save_pretrained("modeltts")

        # Save the model
        model.save_pretrained("modeltts")
        # เชื่อมต่อกับฐานข้อมูล
        db_connection = mysql.connector.connect(**db_config)

        # สร้าง Cursor object เพื่อทำคำสั่ง SQL
        cursor_INSERT = db_connection.cursor()
        # Example:"UPDATE `log` SET `path`= (?) WHERE `questionID` = (?) AND `userID` = ? AND DATE(`date`) = CURDATE()"
        cursor_INSERT.execute("UPDATE `log` SET `text`=%s,`path`= %s WHERE `questionID` = %s AND `userID` = %s AND DATE(`date`) = CURDATE()", (result["text"],dest_path, slide_index,user_id))
        db_connection.commit()
        cursor_INSERT.close()  # ปิด Cursor
        # Here's a simple response for demonstration
        nextpage = {'nextpage': result["text"]}
        return jsonify(nextpage)

    except Exception as e:
        return jsonify({'error': 'Error uploading file: ' + str(e)}), 500


@app.route('/python-endpoint', methods=['POST'])
def index():
    user_id = request.form.get('userID')
    slide_index = request.form.get('slideIndex')
    print("ติดต่อได้")
    try:
        # เชื่อมต่อกับฐานข้อมูล
        connection = mysql.connector.connect(**db_config)

        # สร้าง Cursor object เพื่อทำคำสั่ง SQL
        cursor_SELECT = connection.cursor()

        # ทำคำสั่ง SQL ตรวจสอบข้อมูลในฐานข้อมูล
        # ในที่นี้ไม่ได้ใช้ข้อมูลจริง แค่ตัวอย่างเท่านั้น
        cursor_SELECT.execute("SELECT path FROM log WHERE `questionID` = %s AND `userID` = %s AND DATE(`date`) = CURDATE()", (slide_index, user_id))
        #cursor_SELECT.execute("INSERT INTO `log` (`userID`, `questionID`, `path`) VALUES (?,?,?)")
        # ดึงข้อมูล
        data = cursor_SELECT.fetchall()

        # ปิดการเชื่อมต่อ
        connection.close()
        print(data)
        # ตรวจสอบว่ามีข้อมูลในฐานข้อมูลหรือไม่
        if data:
             # ลิสต์เพื่อเก็บผลลัพธ์ทุกตัว
            # แยกเส้นทางไฟล์เสียงจาก data
            
            
            
            for audio_path in data:
                audio = audio_path[0]  # ตัวอย่างข้อมูลอาจจะอยู่ในคอลัมน์
                print(audio_path[0])
                # ทำต่อไปเพื่อให้ได้ตัวแปร sample ในรูปแบบที่ Transformers ต้องการ
                # นำ audio_path มาใส่ใน Transformers pipeline
                

                # นำตัวแปร sample ไปให้ Transformers pipeline
                result = pipe(audio)
                print(result["text"])
                
                 # เพิ่มการเชื่อมต่อกับฐานข้อมูล
                connection_insert = mysql.connector.connect(**db_config)
                cursor_INSERT = connection_insert.cursor()

                # ทำคำสั่ง SQL เพื่อ INSERT ข้อมูล result["text"]
                sql_INSERT = "UPDATE log SET text = %s WHERE path = %s AND `questionID` = %s AND `userID` = %s AND DATE(`date`) = CURDATE()"
                cursor_INSERT.execute(sql_INSERT, (result["text"], audio,slide_index,user_id))

                # Commit การเปลี่ยนแปลงและปิดการเชื่อมต่อ
                connection_insert.commit()
                connection_insert.close()
                # ส่งข้อมูลกลับไปยัง JavaScript
                nextpage = {'nextpage': result["text"]}
            # ส่งข้อมูลไปยังหน้าเว็บ
            return jsonify(nextpage)
        
        else:
            database = {'database': 'Not have data'+ str(user_id)}
            return jsonify(database)

    except Exception as e:
         # ส่งข้อมูลกลับไปยัง JavaScript ว่ามีข้อผิดพลาด
        error = {'error': str(e)}
        return jsonify(error)
    
@app.route('/python-sound', methods=['POST'])
def index1():
    
    text_post = request.form.get('text')
    # tts = gTTS(text_post, lang='th')
    # tts.save('output1.mp3')
    # Tokenize input text
    inputs = tokenizer(text_post, return_tensors="pt")

    # Generate speech waveform
    with torch.no_grad():
        output = model(**inputs).waveform

    # Convert to numpy array
    audio_waveform = output.cpu().numpy()

    # Save the waveform as a WAV file
    torchaudio.save("output1.wav", torch.tensor(audio_waveform), sample_rate=16000)

    # Save the tokenizer
    tokenizer.save_pretrained("modeltts")

    # Save the model
    model.save_pretrained("modeltts")
    
    # ส่ง URL สาธารณะของไฟล์เสียงกลับ
    return jsonify({'sound': 'output1.wav'})
@app.route('/play/<path:filename>')
def play_sound(filename):
    
    return send_file(filename)

if __name__ == '__main__':
    app.run(host='0.0.0.0',port=5000,debug=True)
    
    
#ยังไม่สามารถให้flaskรันอัตโนมัติได้