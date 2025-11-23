import 'dart:async';

import 'package:app/models/communication.dart';
import 'package:app/providers/chat-provider.dart';
import 'package:app/providers/user_provider.dart';
import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

class ChatPage extends StatefulWidget {
  final int receiverId;
  final String receiverName;

  ChatPage({required this.receiverId, required this.receiverName});

  @override
  State<ChatPage> createState() => _ChatPageState();
}

class _ChatPageState extends State<ChatPage> {
  @override
  void initState() {
    super.initState();
    // Safe place to trigger provider updates
    Future.microtask(() {
    final provider = Provider.of<UserProvider>(context, listen: false).fetchCurrentUser();
    });
  }
  final TextEditingController _msgController = TextEditingController();
  @override
  Widget build(BuildContext context) {
    final user = Provider.of<UserProvider>(context, listen: false).user;
    final chat = Provider.of<ChatProvider>(context, listen: false);
    final loadU = Provider.of<ChatProvider>(context, listen: false).loadUsers();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      if(widget.receiverId != 0) {
        chat.selectUser(widget.receiverId, widget.receiverName);
      }
    });
    return Consumer<ChatProvider>(
      builder: (context, chat, child) {
        return Scaffold(
          appBar: AppBar(
            title: Text("Chat", style: TextStyle(color: Colors.white)),
            backgroundColor: Colors.deepPurple,
            elevation: 1,
            actions: [
              IconButton(
                icon: Icon(Icons.people, color: Colors.white),
                onPressed: chat.toggleUserList,
              ),
            ],
          ),
          body: Row(
            children: [
              // User List
              if (chat.isUserListVisible)
                Container(
                  width: 250,
                  color: Colors.grey[200],
                  child: chat.users.isEmpty
                      ? Center(child: Text("No users found", style: TextStyle(fontSize: 16, color: Colors.red)))
                      : ListView.builder(
                          itemCount: chat.users.length,
                          itemBuilder: (context, index) {
                            final user = chat.users[index];
                            return ListTile(
                              contentPadding: EdgeInsets.symmetric(vertical: 10, horizontal: 16),
                              leading: CircleAvatar(
                                radius: 20,
                                backgroundImage: NetworkImage(
                                  'https://ui-avatars.com/api/?name=${Uri.encodeComponent(user['name'])}&color=FFFFFF&background=263749',
                                ),
                              ),
                              title: Text(user['name'], style: TextStyle(fontWeight: FontWeight.bold)),
                              onTap: () {
                                chat.toggleUserList();
                                chat.selectUser(user['user_id'], user['name']);
                              },
                            );
                          },
                        ),
                ),
              if(!chat.isUserListVisible)
              // Chat Area
              Expanded(
                child: Column(
                  children: [
                    // Chat header
                    Container(
                      padding: EdgeInsets.all(8),
                      color: Colors.grey,
                      child: Row(
                        children: [
                          if (chat.selectedReceiverName != null)
                            CircleAvatar(
                              radius: 20,
                              backgroundImage: NetworkImage(
                                'https://ui-avatars.com/api/?name=${Uri.encodeComponent(chat.selectedReceiverName ?? '')}&color=FFFFFF&background=263749',
                              ),
                            ),
                          SizedBox(width: 8),
                          Text(chat.selectedReceiverName ?? "No one selected", style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold)),
                        ],
                      ),
                    ),

                    // Messages
                    Expanded(
                      child: ListView.builder(
                        reverse: true,
                        itemCount: chat.messages.length,
                        itemBuilder: (context, index) {
                          final msg = chat.messages[chat.messages.length - 1 - index];
                          final isMe = msg['sender_id'] == user?.userId; // Replace with Auth ID
                          return Align(
                            alignment: isMe ? Alignment.centerRight : Alignment.centerLeft,
                            child: Column(
                              crossAxisAlignment:
                                  isMe ? CrossAxisAlignment.end : CrossAxisAlignment.start, // aligns timestamp correctly
                              children: [
                                Container(
                                  margin: EdgeInsets.symmetric(vertical: 4, horizontal: 8),
                                  padding: EdgeInsets.all(10),
                                  decoration: BoxDecoration(
                                    color: isMe ? Colors.green[100] : Colors.grey[300],
                                    borderRadius: BorderRadius.circular(10),
                                  ),
                                  child: Text(msg['message']),
                                ),
                                SizedBox(height: 2),
                                Row(
                                  // mainAxisSize: MainAxisSize.min, 
                                  mainAxisAlignment: isMe ? MainAxisAlignment.end : MainAxisAlignment.start,
                                  children: [
                                    Padding(padding: EdgeInsets.symmetric(horizontal: 4)),
                                    Text(
                                      timeAgo(msg['timestamp']),
                                      style: TextStyle(fontSize: 10, color: Colors.grey[600]),
                                    ),
                                    SizedBox(width: 5),
                                    if (isMe)...[
                                      if(msg['read_status'] == 'sent')
                                        Icon(
                                          Icons.done, size: 14,color: Colors.grey,
                                        ),
                                      if(msg['read_status'] == 'delivered')
                                        Icon(
                                          Icons.done_all,
                                          size: 14,
                                          color: Colors.grey,
                                        ),
                                      if(msg['read_status'] == 'read')
                                        Icon(
                                          Icons.done_all,
                                          size: 14,
                                          color: Colors.blue,
                                        ),
                                    ]
                                  ],
                                ),
                              ],
                            ),
                          );
                        },
                      ),
                    ),

                    // Input box
                    Padding(
                      padding: const EdgeInsets.all(8.0),
                      child: Row(
                        children: [
                          Expanded(
                            child: TextField(
                              controller: _msgController,
                              decoration: InputDecoration(
                                border: OutlineInputBorder(),
                                hintText: "Type a message...",
                              ),
                              textInputAction: TextInputAction.send, // changes keyboard Enter to "Send"
                              onSubmitted: (value) {
                                if (value.trim().isEmpty) return;
                                chat.sendMessage(value);
                                _msgController.clear();
                              },
                            ),
                          ),
                          IconButton(
                            icon: Icon(Icons.send),
                            onPressed: () {
                              chat.sendMessage(_msgController.text);
                              _msgController.clear();
                            },
                          )
                        ],
                      ),
                    ),
                  ],
                ),
              ),
            ],
          ),
        );
      },
    );
  }
}