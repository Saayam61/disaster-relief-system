import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';
import '../models/user.dart';

class UserApi {
  final String baseUrl = 'http://localhost:8000/api';

  Future<User> fetchCurrentUser() async {
    final prefs = await SharedPreferences.getInstance();
    final token = prefs.getString('login_token');
    final response = await http.get(
      Uri.parse('$baseUrl/user'),
      headers: {
        'Authorization': 'Bearer $token',
      },
    );

    if (response.statusCode == 200) {
      return User.fromJson(json.decode(response.body));
    } else {
      throw Exception('Failed to load user');
    }
  }


  Future<void> updateUser({
    required String name,
    required String phone,
    required String email,
    required String address,
  }) async {
    try {
      final prefs = await SharedPreferences.getInstance();
      final token = prefs.getString('login_token');
      final response = await http.post(
        Uri.parse('$baseUrl/updateUser'),
        headers: {
          'Authorization': 'Bearer $token',
          'Content-Type': 'application/json'
        },
        body: json.encode({
          'name': name,
          'phone': phone,
          'email': email,
          'address': address,
        }),
      );

      if (response.statusCode == 200 || response.statusCode == 201) {
        print('User updated successfully!');
      } else {
        print('Update failed with status code: ${response.statusCode}');
        print('Response body: ${response.body}');
        // throw Exception('Failed to update user');
      }
    } catch (e) {
      // print('Exception while updating user: $e');
      // throw Exception('Something went wrong while updating the user');
    }
  }
}