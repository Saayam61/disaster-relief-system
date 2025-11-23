@extends('layouts.app')

@section('content')
<style>
  body {
    background: #f4f6f9;
    overflow-x: hidden;
  }

    .main-content {
        margin-left: 220px;
        transition: all 0.3s;
        padding-top: 56px; 
        min-height: calc(100vh - 56px); 
    }

    .main-content.expanded {
        margin-left: 0;
    }
  .chat-container {
    max-width: 900px;
    height: 600px;
    margin: 20px auto;
    font-family: Arial, sans-serif;
    background: white;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    border-radius: 10px;
    display: flex;
    overflow: hidden;
  }

  .user-list {
    width: 280px;
    border-right: 1px solid #ddd;
    overflow-y: auto;
    padding: 15px;
    background: #fafafa;
  }

  .user-item {
    padding: 10px;
    cursor: pointer;
    border-radius: 6px;
    margin-bottom: 6px;
    transition: background-color 0.2s;
    display: flex;
    align-items: center;
    gap: 10px;
  }
  .user-item:hover, .user-item.active {
    background-color: #e6f7ff;
  }
  .user-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #ccc;
    object-fit: cover;
  }
  .user-name {
    font-weight: 600;
    flex-grow: 1;
  }

  .chat-area {
    flex-grow: 1;
    display: flex;
    flex-direction: column;
    padding: 20px;
  }

  .chat-header {
    font-size: 20px;
    font-weight: bold;
    margin-bottom: 15px;
    display: flex;
    align-items: center;
    gap: 10px;
  }

  .chat-header img {
    width: 45px;
    height: 45px;
    border-radius: 50%;
  }

  #chat-box {
    flex-grow: 1;
    border: 1px solid #ccc;
    border-radius: 8px;
    padding: 15px;
    overflow-y: auto;
    background: #fafafa;
    display: flex;
    flex-direction: column;
    gap: 10px;
  }

  .message-input-container {
    margin-top: 10px;
    display: flex;
    gap: 10px;
  }

  #message-input {
    flex-grow: 1;
    padding: 10px;
    font-size: 16px;
    border-radius: 6px;
    border: 1px solid #ccc;
  }

  #send-btn {
    padding: 10px 20px;
    font-size: 16px;
    border-radius: 6px;
    border: none;
    background-color: #22c63d;
    color: white;
    cursor: pointer;
    transition: background-color 0.3s;
  }
  #send-btn:disabled {
    background-color: #a0dca0;
    cursor: not-allowed;
  }
</style>
<div id="main-content" class="main-content">
<div class="chat-container">

  <div class="user-list" id="user-list">
    {{-- User list will be loaded here by JS --}}
    <p style="color:#999;">Loading users...</p>
  </div>

  <div class="chat-area">
    <div class="chat-header" id="chat-header">
      <img src="https://ui-avatars.com/api/?name=No+One&color=FFFFFF&background=999999" alt="Avatar" id="chat-header-avatar" />
      <span id="chat-with-name">No one selected</span>
    </div>

    <div id="chat-box">
      <em style="color: #999;">Select a user to start chatting.</em>
    </div>

    <div class="message-input-container">
      <input
        type="text"
        id="message-input"
        placeholder="Type your message..."
        disabled
      />
      <button id="send-btn" disabled>Send</button>
    </div>
  </div>
</div>
</div>
@endsection

@push('scripts')
<script>
  let currentUserId = Number("{{ Auth::id() }}");
  let receiverIdFromSearch = {{ Js::from($receiverId)}};
  let receiverNameFromSearch = {{ Js::from($receiverName)}};
  let selectedReceiverId = null;
  let selectedReceiverName = null;

  const userListEl = document.getElementById('user-list');
  const chatBox = document.getElementById('chat-box');
  const messageInput = document.getElementById('message-input');
  const sendBtn = document.getElementById('send-btn');
  const chatHeaderName = document.getElementById('chat-with-name');
  const chatHeaderAvatar = document.getElementById('chat-header-avatar');

  if (receiverIdFromSearch && receiverNameFromSearch) {
  // Directly load the chat from search
  selectedReceiverId = receiverIdFromSearch;
  selectedReceiverName = receiverNameFromSearch;

  chatHeaderName.textContent = receiverNameFromSearch;
  chatHeaderAvatar.src = `https://ui-avatars.com/api/?name=${encodeURIComponent(receiverNameFromSearch)}&color=FFFFFF&background=263749`;
  messageInput.disabled = false;
  sendBtn.disabled = false;

  fetchChat(receiverIdFromSearch);
} else {
  fetchUsers(); // Only load the user list if we're not coming directly from search
}
  // Fetch user list (adjust URL to your route that returns JSON of users)
  function fetchUsers() {
    fetch('/search/chat') // you need to make this endpoint return your user list
      .then(res => res.json())
      .then(users => {
        userListEl.innerHTML = '';
        users.forEach(user => {
          if(user.user_id === currentUserId) return; // skip self

          const userItem = document.createElement('div');
          userItem.classList.add('user-item');
          userItem.dataset.userid = user.user_id;

          const avatar = document.createElement('img');
          avatar.src = `https://ui-avatars.com/api/?name=${encodeURIComponent(user.name)}&color=FFFFFF&background=263749`;
          avatar.classList.add('user-avatar');

          const nameSpan = document.createElement('span');
          nameSpan.textContent = user.name;
          nameSpan.classList.add('user-name');

          userItem.appendChild(avatar);
          userItem.appendChild(nameSpan);

          userItem.addEventListener('click', () => {
            selectUser(user.user_id, user.name);
          });

          userListEl.appendChild(userItem);
        });
      })
      .catch(() => {
        userListEl.innerHTML = '<p style="color:red;">Failed to load users.</p>';
      });
  }

  // Select a user to chat with
  function selectUser(userId, userName) {
    selectedReceiverId = userId;
    selectedReceiverName = userName;

    // Update UI
    chatHeaderName.textContent = userName;
    chatHeaderAvatar.src = `https://ui-avatars.com/api/?name=${encodeURIComponent(userName)}&color=FFFFFF&background=263749`;
    messageInput.disabled = false;
    sendBtn.disabled = false;

    // Highlight selected user in the list
    document.querySelectorAll('.user-item').forEach(el => el.classList.remove('active'));
    const selectedEl = [...document.querySelectorAll('.user-item')].find(el => Number(el.dataset.userid) === userId);
    if(selectedEl) selectedEl.classList.add('active');

    // Load chat
    fetchChat(userId);
  }

  // Format timestamp nicely
  function formatTimestamp(ts) {
    const date = new Date(ts);
    return date.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
  }

  // Create status badge
  function createStatusBadge(status) {
    const badge = document.createElement('span');
    badge.style.fontSize = '10px';
    badge.style.color = '#666';
    badge.style.marginLeft = '8px';
    badge.style.marginTop = '4px';

    switch(status) {
      case 'sent':
        badge.textContent = 'Sent';
        break;
      case 'delivered':
        badge.textContent = 'Delivered';
        break;
      case 'read':
        badge.textContent = 'Read';
        break;
      default:
        badge.textContent = status;
    }
    return badge;
  }

  // Fetch chat messages
  function fetchChat(receiverId) {
    if (!receiverId) {
      chatBox.innerHTML = '<em style="color: #999;">Select a user to start chatting.</em>';
      messageInput.disabled = true;
      sendBtn.disabled = true;
      return;
    }
    fetch(`/messages/chat/${receiverId}?mark_read=true`)
      .then(res => res.json())
      .then(messages => {
        chatBox.innerHTML = '';
        if(messages.length === 0) {
          chatBox.innerHTML = '<em style="color: #999;">No messages yet. Say hi!</em>';
          return;
        }
        messages.forEach(msg => {
          const msgDiv = document.createElement('div');
          msgDiv.style.padding = '8px 12px';
          msgDiv.style.maxWidth = '70%';
          msgDiv.style.borderRadius = '15px';
          msgDiv.style.wordBreak = 'break-word';
          msgDiv.style.display = 'inline-block';
          msgDiv.style.position = 'relative';

          // Content with message text
          msgDiv.textContent = msg.message;

          // Timestamp below message text, smaller and lighter
          const timeSpan = document.createElement('div');
          timeSpan.style.fontSize = '10px';
          timeSpan.style.color = '#666';
          timeSpan.style.marginTop = '4px';
          timeSpan.textContent = formatTimestamp(msg.timestamp);

          // Status badge only for messages sent by current user
          if(msg.sender_id === currentUserId) {
            msgDiv.style.backgroundColor = '#dcf8c6'; // light green
            msgDiv.style.alignSelf = 'flex-end';
            msgDiv.style.textAlign = 'right';
            msgDiv.style.marginLeft = 'auto';

            const statusBadge = createStatusBadge(msg.read_status);
            timeSpan.appendChild(statusBadge);
          } else {
            msgDiv.style.backgroundColor = '#f1f0f0'; // light gray
            msgDiv.style.alignSelf = 'flex-start';
            msgDiv.style.textAlign = 'left';
            msgDiv.style.marginRight = 'auto';
          }

          // Append time + status below message text
          msgDiv.appendChild(timeSpan);

          chatBox.appendChild(msgDiv);
        });

        // Scroll to bottom after rendering messages
        chatBox.scrollTop = chatBox.scrollHeight;

        messageInput.disabled = false;
        sendBtn.disabled = false;
      })
      .catch(() => {
        chatBox.innerHTML = '<em style="color: red;">Failed to load messages.</em>';
      });
  }

  // Send message handler
  sendBtn.addEventListener('click', () => {
    const message = messageInput.value.trim();
    if (!message || !selectedReceiverId) return alert('Please enter a message and select a user.');

    sendBtn.disabled = true;
    fetch('/messages/send', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
      },
      body: JSON.stringify({ receiver_id: selectedReceiverId, message }),
    })
    .then(res => res.json())
    .then(data => {
      sendBtn.disabled = false;
      if(data.success) {
        messageInput.value = '';
        fetchChat(selectedReceiverId);
      } else {
        alert('Failed to send message.');
      }
    })
    .catch(() => {
      sendBtn.disabled = false;
      alert('Error sending message.');
    });
  });

  // Optional: Send message on Enter key
  messageInput.addEventListener('keypress', e => {
    if(e.key === 'Enter') {
      sendBtn.click();
      e.preventDefault();
    }
  });

  // Poll new messages every 3 seconds if chat open
  setInterval(() => {
    if(selectedReceiverId) fetchChat(selectedReceiverId);
  }, 3000);

  // Initial user list fetch
  fetchUsers();
</script>
@endpush
