import 'package:app/models/news_feed.dart';
import 'package:app/services/newsfeed_api.dart';
import 'package:flutter/material.dart';

class NewsFeedProvider with ChangeNotifier {
  List<NewsFeed> _posts = [];
  bool _isLoading = false;

  List<NewsFeed> get posts => _posts;
  bool get isLoading => _isLoading;

  Future<void> fetchPosts() async {
    _isLoading = true;
    notifyListeners();
    try {
      _posts = await NewsFeedApi.fetchPosts();
    } catch (e) {
      print('Error loading posts: ${e.toString()}');
    }
    _isLoading = false;
    notifyListeners();
  }
}