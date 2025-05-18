import 'dart:convert';
import 'package:app/models/news_feed.dart';
import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';

class NewsFeedApi {
  static const String baseUrl = 'http://localhost:8000/api';
  static Future<List<NewsFeed>> fetchPosts() async {
    final prefs = await SharedPreferences.getInstance();
    final token = prefs.getString('login_token');
    final res = await http.get(
      Uri.parse('$baseUrl/news-feed'),
      headers: {
        'Authorization': 'Bearer $token',
        'Accept': 'application/json',
      },
    );

    if (res.statusCode == 200) {
      List<dynamic> outerList = json.decode(res.body);
      List<dynamic> flattened = outerList.expand((i) => i).toList();
      return flattened.map((post) => NewsFeed.fromJson(post)).toList();
    } else {
      throw Exception('Failed to load news feed');
    }
  }
}