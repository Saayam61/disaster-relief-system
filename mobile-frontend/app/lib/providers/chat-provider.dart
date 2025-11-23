import 'dart:async';

import 'package:app/services/chat_api.dart';
import 'package:flutter/material.dart';

class ChatProvider with ChangeNotifier {
  ChatApi _api = ChatApi();

  List<dynamic> users = [];
  List<dynamic> messages = [];
  bool isUserListVisible = true;
  int? selectedReceiverId;
  String? selectedReceiverName;

  Timer? _autoRefreshTimer;

  Future<void> loadUsers() async {
    try{
    users = await _api.fetchUsers();
    notifyListeners();
    } catch (e) {
      print(e.toString());
    }
  }

  Future<void> loadMessages(int receiverId) async {
    try{
      messages = await _api.fetchChat(receiverId);
      notifyListeners();
    } catch (e) {
      print(e.toString());
    }

  }

  Future<void> sendMessage(String message) async {
    if (selectedReceiverId != null && message.trim().isNotEmpty) {
      bool success = await _api.sendMessage(selectedReceiverId!, message);
      if (success) {
        await loadMessages(selectedReceiverId!);
      }
    }
    else {
      throw Exception('No receiver selected or message is empty');
    }
  }

  void selectUser(int id, String name) {
    selectedReceiverId = id;
    selectedReceiverName = name;
    loadMessages(id);

    _autoRefreshTimer?.cancel();

    _autoRefreshTimer = Timer.periodic(Duration(seconds: 1), (_) {
      loadMessages(id);
    });
    notifyListeners();
  }

  void toggleUserList() {
    isUserListVisible = !isUserListVisible;
    notifyListeners();
  }
}
