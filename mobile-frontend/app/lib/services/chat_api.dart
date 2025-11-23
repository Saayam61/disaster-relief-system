import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';

class ChatApi {
  final String baseUrl = 'http://localhost:8000/api';

  Future<List<dynamic>> fetchUsers() async {
    final prefs = await SharedPreferences.getInstance();
    final token = prefs.getString('login_token');
    final res = await http.get(Uri.parse('$baseUrl/search/chat'), headers: {
    'Content-Type': 'application/json',
    'Authorization': 'Bearer $token',
    },);
    if (res.statusCode == 200) {
      return jsonDecode(res.body);
    }
    throw Exception('Failed to load users');
  }

  Future<List<dynamic>> fetchChat(int receiverId) async {
    final prefs = await SharedPreferences.getInstance();
    final token = prefs.getString('login_token');
    final res = await http.get(
      Uri.parse('$baseUrl/messages/chat/$receiverId?mark_read=true'),
      headers: {
        'Content-Type': 'application/json',
        'Authorization': 'Bearer $token',
      },
      );
    if (res.statusCode == 200) {
      return jsonDecode(res.body);
    }else{
      throw Exception('Failed to load chat messages');
    }
  }

  Future<bool> sendMessage(int receiverId, String message) async {
    final prefs = await SharedPreferences.getInstance();
    final token = prefs.getString('login_token');
    final res = await http.post(
      Uri.parse('$baseUrl/messages/send'),
      headers: {
    'Content-Type': 'application/json',
    'Authorization': 'Bearer $token',
    },
      body: jsonEncode({'receiver_id': receiverId, 'message': message}),
    );
    if (res.statusCode == 200) {
      final data = jsonDecode(res.body);
      return data['success'] == true;
    }
    return false;
  }
}
